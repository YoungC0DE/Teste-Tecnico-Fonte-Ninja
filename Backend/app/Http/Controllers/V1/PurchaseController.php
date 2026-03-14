<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
                ->orWhereLike('total_value', "%$search%")
                ->with('items.product')
                ->paginate();
        } else {
            $purchases = Purchase::with('items.product')->paginate();
        }

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
     * @bodyParam items array required Lista de itens da compra.
     * @bodyParam items[].product_id integer required ID do produto. Example: 5
     * @bodyParam items[].quantity integer required Quantidade comprada do produto. Example: 10
     * @bodyParam items[].unit_cost number required Custo unitário do produto na compra. Example: 25.50
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
                'total_value' => 0
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if (empty($product)) {
                    throw new \Exception("Produto com ID {$item['product_id']} não encontrado.");
                }

                $quantity = $item['quantity'];
                $unitCost = $item['unit_cost'];
                $totalCost = $quantity * $unitCost;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost
                ]);

                // cálculo custo médio
                $currentStock = $product->stock;
                $currentAverageCost = $product->average_cost;

                $newStock = $currentStock + $quantity;

                $newAverageCost =
                    (($currentStock * $currentAverageCost) + ($quantity * $unitCost))
                    / $newStock;

                $product->update([
                    'stock' => $newStock,
                    'average_cost' => $newAverageCost
                ]);

                $total += $totalCost;
            }

            $purchase->update([
                'total_value' => $total
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
