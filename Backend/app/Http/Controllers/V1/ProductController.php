<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Listar produtos
     *
     * Retorna uma lista paginada de produtos cadastrados.
     * 
     * É possível realizar uma busca utilizando o parâmetro `search`,
     * que será aplicado nos campos `name` e `sku`.
     *
     * Exemplo:
     * - /products
     * - /products?search=notebook
     *
     * @param Request $request
     * @queryParam search string Opcional. Termo para busca por nome ou SKU do produto. Example: /products?search=notebook
     * 
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('search')) {
            $search = trim(strtolower($request->input('search')));
            $products = Product::whereLike('name', "%$search%")
                ->orWhereLike('sku', "%$search%")
                ->paginate();
        } else {
            $products = Product::paginate();
        }

        return $this->jsonResponse($products);
    }

    /**
     * Criar produtos
     *
     * Cria um ou mais produtos no sistema.
     * 
     * O endpoint recebe um array de produtos no campo `data`.
     * Para cada produto, é verificado se já existe um produto com o mesmo `sku`.
     * 
     * Regras:
     * - Produtos com SKU duplicado são ignorados.
     * - Produtos válidos são criados com `stock` inicial igual a 0.
     * - O custo médio (`average_cost`) é iniciado com 0.
     * 
     * Ao final da execução, é retornado um resumo contendo:
     * - Quantidade de produtos criados
     * - Quantidade de erros
     * - Quantidade de produtos duplicados
     *
     * @param StoreProductRequest $request
     * 
     * @bodyParam data array required Lista de produtos a serem criados.
     * @bodyParam data[].name string required Nome do produto. Example: "Notebook Dell".
     * @bodyParam data[].sku string required SKU único do produto. Example: "NOTEBOOK-DELL-123".
     * 
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $success = 0;
        $error = 0;
        $duplicated = 0;

        $products = $request->input('data', []);

        foreach ($products as $product) {
            if (Product::where('sku', $product['sku'])->exists()) {
                $duplicated++;
                continue;
            }

            try {
                Product::create([
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'stock' => 0,
                    'average_cost' => 0
                ]);

                $success++;
            } catch (\Exception $e) {
                Log::error("Error creating product: " . $e->getMessage());
                $error++;
            }
        }

        return $this->successResponse("Criados: $success, Erros: $error, Duplicados: $duplicated", 201);
    }

    /**
     * Exibir produto
     *
     * Retorna os detalhes de um produto específico com base no seu ID.
     *
     * Caso o produto não seja encontrado, será retornado erro 404.
     *
     * @param string $productId
     * @urlParam productId string required ID do produto. Example: 12
     * 
     * @return JsonResponse
     */
    public function show(string $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (empty($product)) {
            return $this->errorResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($product);
    }

    /**
     * Atualizar produto
     *
     * Atualiza os dados de um produto existente.
     * 
     * Os campos que podem ser atualizados são:
     * - `name`
     * - `sku`
     *
     * Caso o produto não seja encontrado, será retornado erro 404.
     *
     * @param string $productId
     * @param Request $request
     * 
     * @urlParam productId string required ID do produto. Example: 12
     * 
     * @bodyParam name string Nome do produto. Example: "Notebook Dell Inspiron".
     * @bodyParam sku string SKU do produto. Example: "NOTEBOOK-DELL-INSPIRON-123".
     * 
     * @return JsonResponse
     */
    public function update(string $productId, Request $request): JsonResponse
    {
        $product = Product::find($productId);

        if (empty($product)) {
            return $this->errorResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        $product->update(
            $request->only(['name', 'sku'])
        );

        return $this->jsonResponse($product);
    }

    /**
     * Remover produto
     *
     * Remove um produto do sistema com base no seu ID.
     *
     * Caso o produto não exista, será retornado erro 404.
     *
     * @param string $productId
     * 
     * @urlParam productId string required ID do produto. Example: 12
     * 
     * @return JsonResponse
     */
    public function destroy(string $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (empty($product)) {
            return $this->errorResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        $product->delete();

        return $this->jsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
