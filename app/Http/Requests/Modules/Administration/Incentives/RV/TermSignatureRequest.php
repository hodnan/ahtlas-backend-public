<?php

namespace App\Http\Requests\Modules\Administration\Incentives\RV;

use App\Traits\FailedValidation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TermSignatureRequest extends FormRequest
{
    use FailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'signature' => ['required', 'integer',  Rule::unique('modules.rv_terms_signatures', 'id')
            ->where('username', Auth::user()->username)
            ->whereNotNull('uuid')
        ],
            'accept' => ['required', 'integer']
            
        ];
    }

    public function messages()
    {
        return [
            'signature.unique' => 'Falha! o Termo já possui assinatura.',

        ];
    }

   
}
