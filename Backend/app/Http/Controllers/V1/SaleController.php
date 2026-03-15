<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\StoreSaleRequest;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    /**
     * Listar vendas
     *
     * Retorna uma lista paginada de vendas registradas no sistema.
     *
     * Cada venda inclui seus itens e os respectivos produtos
     * através do relacionamento `items.product`.
     *
     * É possível realizar uma busca utilizando o parâmetro `search`,
     * que será aplicado nos campos:
     * - `id` da venda
     * - `total_value` da venda
     *
     * Exemplos:
     * - /sales
     * - /sales?search=1
     * - /sales?search=500
     *
     * @param Request $request
     *
     * @queryParam search string Opcional. Termo de busca para ID ou valor total da venda. Example: /sales?search=500
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('search')) {
            $search = trim(strtolower($request->input('search')));
            $sales = Sale::whereLike('id', "%$search%")
                ->orWhereLike('valor_total', "%$search%")
                ->with('items.product')
                ->paginate();
        } else {
            $sales = Sale::with('items.product')->paginate();
        }

        // Adaptar saída para os campos esperados pelo frontend
        $sales->getCollection()->transform(function (Sale $sale) {
            $data = $sale->toArray();
            $data["created_at"] = Carbon::parse($sale->created_at)->format('Y-m-d');
            $data["updated_at"] = Carbon::parse($sale->updated_at)->format('Y-m-d');

            // Formatação monetária para exibição (R$ 100,00)
            $data["valor_total"] = 'R$ ' . number_format((float) $sale->valor_total, 2, ',', '.');

            return $data;
        });

        return $this->jsonResponse($sales);
    }

    /**
     * Criar venda
     *
     * Registra uma nova venda no sistema e seus respectivos itens.
     *
     * Para cada item informado:
     * - O produto é validado pelo `product_id`
     * - É verificado se existe estoque suficiente
     * - O item da venda é registrado
     * - O estoque do produto é decrementado
     * - O lucro do item é calculado
     *
     * O lucro é calculado utilizando a fórmula:
     *
     * lucro = (preco_venda - custo_medio_produto) * quantidade
     *
     * O custo médio utilizado é o `average_cost` armazenado no produto,
     * que representa o custo médio ponderado baseado nas compras anteriores.
     *
     * Todo o processo ocorre dentro de uma **transação de banco de dados**
     * para garantir consistência caso ocorra algum erro durante a operação.
     *
     * Caso:
     * - O produto não exista
     * - Ou o estoque seja insuficiente
     *
     * A operação será cancelada e nenhuma alteração será persistida.
     *
     * Ao final da operação:
     * - O valor total da venda é calculado automaticamente
     * - A venda é retornada com seus itens e produtos relacionados
     *
     * @param StoreSaleRequest $request
     *
     * @bodyParam cliente string required Nome do cliente. Example: "Fulano da Silva".
     * @bodyParam produtos array required Lista de produtos da venda.
     * @bodyParam produtos[].id integer required ID do produto. Example: 5
     * @bodyParam produtos[].quantidade integer required Quantidade vendida do produto. Example: 10
     * @bodyParam produtos[].preco_unitario number required Preço unitário de venda do produto. Example: 50.00
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'valor_total' => 0,
                'cliente' => $request->input('cliente'),
            ]);

            $total = 0;
            $profitTotal = 0;

            foreach ($request->input('produtos', []) as $item) {
                $product = Product::find($item['id']);

                if (empty($product)) {
                    throw new \Exception("Produto com ID {$item['id']} não encontrado.");
                }

                $quantity = $item['quantidade'];
                $unitPrice = $item['preco_unitario'];

                if ($product->estoque < $quantity) {
                    throw new \Exception("Estoque insuficiente para o produto {$product->nome}. Estoque disponível: {$product->estoque}");
                }

                $unitCost = $product->custo_medio;
                $totalPrice = $quantity * $unitPrice;
                $profit = ($unitPrice - $unitCost) * $quantity;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantidade' => $quantity,
                    'preco_unitario' => $unitPrice,
                    'total_preco' => $totalPrice,
                    'custo_unitario' => $unitCost,
                    'lucro' => $profit
                ]);

                $product->decrement('estoque', $quantity);
                $total += $totalPrice;
                $profitTotal += $profit;
            }

            $sale->update([
                'valor_total' => $total
            ]);
            DB::commit();

            return $this->jsonResponse([
                'sale' => $sale->load('items.product'),
                'total' => $total,
                'profit' => $profitTotal,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
