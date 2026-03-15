<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente' => ['required', 'string', 'max:255'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.id' => ['required', 'integer', 'exists:products,id'],
            'produtos.*.quantidade' => ['required', 'integer', 'min:1'],
            'produtos.*.preco_unitario' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente.required' => 'O nome do cliente é obrigatório',
            'cliente.string' => 'O cliente deve ser um texto',
            'cliente.max' => 'O cliente deve ter no máximo 255 caracteres',
            'produtos.required' => 'Adicione pelo menos um produto',
            'produtos.array' => 'Os produtos devem ser um array',
            'produtos.min' => 'Adicione pelo menos um produto',
            'produtos.*.id.required' => 'Selecione um produto',
            'produtos.*.id.integer' => 'O ID do produto deve ser um número',
            'produtos.*.id.exists' => 'O produto selecionado não existe',
            'produtos.*.quantidade.required' => 'A quantidade é obrigatória',
            'produtos.*.quantidade.integer' => 'A quantidade deve ser um número inteiro',
            'produtos.*.quantidade.min' => 'A quantidade deve ser no mínimo 1',
            'produtos.*.preco_unitario.required' => 'O preço unitário é obrigatório',
            'produtos.*.preco_unitario.numeric' => 'O preço deve ser um número',
            'produtos.*.preco_unitario.min' => 'O preço deve ser no mínimo R$ 0,01',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST)
        );
    }
}
