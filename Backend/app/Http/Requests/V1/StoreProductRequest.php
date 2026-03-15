<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:255'],
            'preco_venda' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do produto é obrigatório',
            'nome.string' => 'O nome deve ser um texto',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres',
            'nome.max' => 'O nome deve ter no máximo 255 caracteres',
            'preco_venda.required' => 'O preço de venda é obrigatório',
            'preco_venda.numeric' => 'O preço deve ser um número',
            'preco_venda.min' => 'O preço deve ser no mínimo R$ 0,01',
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
