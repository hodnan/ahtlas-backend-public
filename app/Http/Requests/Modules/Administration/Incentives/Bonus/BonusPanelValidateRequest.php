<?php


namespace App\Http\Requests\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Traits\FailedValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BonusPanelValidateRequest extends FormRequest
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
            'fiscal_year_id' => ['required'],
            'status' => 'required|in:2,3', 
        ];
    }

    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {        
            $this->validateFiscalYear($validator);
        });
    }

    // Verifica se ano fiscal é ativo 
    protected function validateFiscalYear($validator)
    {
        $fiscalYear = BonusFiscalYear::where('public_id', $this->input('fiscal_year_id'))->first();

        if ($fiscalYear && !$fiscalYear->active['id']) {
            $validator->errors()->add('fiscal_year_id', 'Resgistro só é premitido em ano físcal ativo ');
        }
    }
}
