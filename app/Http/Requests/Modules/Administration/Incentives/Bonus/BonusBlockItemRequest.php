<?php


namespace App\Http\Requests\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Services\Modules\Administration\BonusBlockService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Traits\FailedValidation;
use Illuminate\Support\Facades\Log;

class BonusBlockItemRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([]);
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
            'owner_id' => 'required',
            'block_id' => 'required',
            'leader_id' => 'required',
            'area' => 'required',
            'proof' => 'required',
            'indicator_id' => [
                'required',
                'integer',
                Rule::unique('modules.bonus_block_items', 'indicator_id')
                    ->where('owner_id', $this->input('owner_id'))
                    ->where('block_id', $this->input('block_id'))
                    ->where('fiscal_year_id', $this->input('fiscal_year_id'))
                    ->ignore($this->route('bonusBlockItem') ? $this->route('bonusBlockItem')->id : '', 'id')
            ],
            'accumulation_type' => 'required|integer',
            'weight' => 'required|numeric|min:0|max:100',
            'range' => 'required|array',
            'range.*.gr' => 'required|numeric',

            'targets.*.accumulated_result' => 'nullable|numeric',
            'targets.*.accumulated_target' => 'nullable|numeric',
            'targets.*.month_result' => 'nullable|numeric',
            'targets.*.month_target' => 'nullable|numeric',

        ];
    }

    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {
            $this->validateBlockWeight($validator);             
            $this->validateFiscalYear($validator);           
        });
    }

    // Verifica se o peso total do bloco é maior que 100
    protected function validateBlockWeight($validator)
    {

        $id = $this->route('bonusBlockItem') ? $this->route('bonusBlockItem')->id : null;


        $blockWeight = BonusBlockService::getFullweight($this->input('block_id'), $id );

        $blockWeight = $blockWeight  + $this->input('weight');
        if ($blockWeight > 100) {
            $validator->errors()->add('weight', "Peso total do bloco não pode ser maior que 100");
        }
    }
   
    // Verifica se ano fiscal é ativo 
    protected function validateFiscalYear($validator)
    {        
        $fiscalYear = BonusFiscalYear::where('public_id', $this->input('fiscal_year_id'))->first();
       
       
        if (!$fiscalYear->active['id']) {
            $validator->errors()->add('fiscal_year_id','Resgistro só é premitido em ano físcal ativo ');
        }
    }
}
