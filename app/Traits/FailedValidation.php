<?php

namespace App\Traits;

use App\Services\Core\ApiResponse\ApiResponseInterface;
use Illuminate\Contracts\Validation\Validator; // Importação correta para Validator
use Illuminate\Validation\ValidationException; // Importação para ValidationException

trait FailedValidation
{
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Os dados fornecidos são inválidos.',
            'errors' => $validator->errors()
        ], ApiResponseInterface::CODE_INVALID_DATA); // Altere o código de status aqui conforme necessário

        throw new ValidationException($validator, $response);
    }
}
