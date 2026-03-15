<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    /**
     * Listar compras
     *
     * Retorna uma lista paginada de compras registradas no sistema.
     *
     * Cada compra inclui seus itens e os respectivos produtos
     * através do relacionamento `items.product`.
     *
     * É possível realizar uma busca utilizando o parâmetro `search`,
     * que será aplicado nos campos:
     * - `id` da compra
     * - `total_value` da compra
     *
     * Exemplos:
     * - /purchases
     * - /purchases?search=1
     * - /purchases?search=250
     *
     * @param Request $request
     *
     * @queryParam search string Opcional. Termo de busca para ID ou valor total da compra. Example: /purchases?search=250
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('search')) {
            $search = trim(strtolower($request->input('search')));
            $purchases = Purchase::whereLike('id', "%$search%")
                ->orWhereLike('valor_total', "%$search%")
                ->with('items.product')
                ->paginate();
        } else {
            $purchases = Purchase::with('items.product')->paginate();
        }

        // Adaptar saída para os campos esperados pelo frontend
        $purchases->getCollection()->transform(function (Purchase $purchase) {
            $data = $purchase->toArray();
            $data["created_at"] = Carbon::parse($purchase->created_at)->format('Y-m-d');
            $data["updated_at"] = Carbon::parse($purchase->updated_at)->format('Y-m-d');

            // Formatação monetária para exibição (R$ 100,00)
            $data["valor_total"] = 'R$ ' . number_format((float) $purchase->valor_total, 2, ',', '.');

            return $data;
        });

        return $this->jsonResponse($purchases);
    }

    /**
     * Criar compra
     *
     * Registra uma nova compra no sistema e seus respectivos itens.
     *
     * Para cada item informado:
     * - O produto é validado pelo `product_id`
     * - É criado um registro em `purchase_items`
     * - O estoque do produto é atualizado
     * - O custo médio do produto é recalculado
     *
     * O custo médio é calculado utilizando a fórmula:
     *
     * novo_custo_medio =
     * ((estoque_atual * custo_medio_atual) + (quantidade_compra * custo_unitario))
     * / novo_estoque
     *
     * Todo o processo ocorre dentro de uma **transação de banco de dados**,
     * garantindo consistência das informações caso ocorra algum erro.
     *
     * Caso algum produto informado não exista, a operação será cancelada.
     *
     * Ao final da operação:
     * - O valor total da compra é calculado automaticamente
     * - A compra é retornada com seus itens e produtos relacionados
     *
     * @param StorePurchaseRequest $request
     *
     * @bodyParam fornecedor string required Nome do fornecedor. Example: "Fornecedor X".
     * @bodyParam produtos array required Lista de produtos da compra.
     * @bodyParam produtos[].id integer required ID do produto. Example: 5
     * @bodyParam produtos[].quantidade integer required Quantidade comprada do produto. Example: 10
     * @bodyParam produtos[].preco_unitario number required Preço unitário da compra. Example: 25.50
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function store(StorePurchaseRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'valor_total' => 0,
                'fornecedor' => $request->input('fornecedor'),
            ]);

            $total = 0;
            foreach ($request->input('produtos', []) as $item) {
                $product = Product::find($item['id']);

                if (empty($product)) {
                    throw new \Exception("Produto com ID {$item['id']} não encontrado.");
                }

                $quantity = $item['quantidade'];
                $unitCost = $item['preco_unitario'];
                $totalCost = $quantity * $unitCost;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantidade' => $quantity,
                    'preco_unitario' => $unitCost,
                    'total_custo' => $totalCost,
                ]);

                // cálculo custo médio
                $currentStock = $product->estoque;
                $currentAverageCost = $product->custo_medio;

                $newStock = $currentStock + $quantity;

                $newAverageCost =
                    (($currentStock * $currentAverageCost) + ($quantity * $unitCost))
                    / $newStock;

                $product->update([
                    'estoque' => $newStock,
                    'custo_medio' => $newAverageCost
                ]);

                $total += $totalCost;
            }

            $purchase->update([
                'valor_total' => $total
            ]);

            DB::commit();

            return $this->jsonResponse(
                $purchase->with('items.product')->find($purchase->id),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
