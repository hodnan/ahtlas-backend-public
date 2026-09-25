<?php


namespace App\Http\Requests\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Traits\FailedValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BonusBlockRequest extends FormRequest
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
        $this->merge([
            'name' => ucfirstException($this->input('name')),
        ]);
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
            'name' => [
                'required',
                Rule::unique('modules.bonus_blocks', 'name')
                    ->where('fiscal_year_id', $this->input('fiscal_year_id'))
                    ->where(function ($query) {
                        if ($this->input('owner_id')) {
                            $query->where('owner_id', $this->input('owner_id'));
                        }
                    })
                    ->ignore($this->route('bonusBlock') ? $this->route('bonusBlock')->public_id : '', 'public_id'),
            ],
            'weight' => 'required|numeric',
            'order' => 'required|integer',
            'default' => 'nullable|in:0,1,true,false',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateFiscalYear($validator);
        });
    }

    // Verifica se ano fiscal é ativo 
    protected function validateFiscalYear($validator)
    {        
        $fiscalYear = BonusFiscalYear::where('public_id', $this->input('fiscal_year_id'))->first();
       
        if ($fiscalYear && !$fiscalYear->active['id']) {
            $validator->errors()->add('fiscal_year_id','Resgistro só é premitido em ano físcal ativo ');
        }
    }
}
