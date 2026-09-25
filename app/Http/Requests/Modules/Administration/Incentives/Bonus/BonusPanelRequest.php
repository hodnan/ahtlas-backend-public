<?php


namespace App\Http\Requests\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Traits\FailedValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BonusPanelRequest extends FormRequest
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
        $blocks = $this->input('blocks', []);

        // Converte 'true'/'false' em booleanos em todos os blocos
        $convertedBlocks = array_map(function ($block) {
            if (isset($block['delete'])) {
                $block['delete'] = filter_var($block['delete'], FILTER_VALIDATE_BOOLEAN);
            }
            return $block;
        }, $blocks);

        $this->merge([
            'panel' => $this->input('fiscal_year_id') ?? null,
            'blocks' => $convertedBlocks,

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
            'panel' => [
                Rule::unique('modules.bonus_panels', 'fiscal_year_id')
                    ->where('fiscal_year_id', $this->input('fiscal_year_id'))
                    ->where('area', $this->input('area'))
                    ->where('hierarchical_level', $this->input('hierarchical_level'))
                    ->where('owner_id', $this->input('owner_id'))
                    ->ignore($this->route('bonusPanel') ? $this->route('bonusPanel')->public_id : '', 'public_id'),
            ],
            'fiscal_year_id' => ['required'],
            'owner_id' => ['required'],
            'status' => 'nullable|in:0,1',
            'area' => ['required'],
            'hierarchical_level' => ['required', 'integer'],
            'blocks' => 'required|array|min:1',
            'blocks.*.block_id' => ['required', 'distinct'],
            'blocks.*.weight' => ['required', 'numeric'],

        ];
    }

    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {
            $this->validatekWeight($validator);
            $this->validateFiscalYear($validator);
        });
    }

    // Verifica se o peso total do bloco é maior que 100
    protected function validatekWeight($validator)
    {
        $blocks = $this->input('blocks');

        // Filtra apenas os blocos onde 'delete' é false
        $activeBlocks = $blocks ? array_filter($blocks, function ($block) {
            return !isset($block['delete']) || $block['delete'] === false;
        }) : [];

        // Calcula o peso total apenas dos blocos filtrados
        $totalWeight = $activeBlocks ? array_sum(array_column($activeBlocks, 'weight')) : null;

        // Calcula o peso total apenas dos blocos filtrados
        $totalWeight = $activeBlocks ? array_sum(array_column($activeBlocks, 'weight')) : null;

        if ($totalWeight && $totalWeight > 100) {
            $validator->errors()->add('full_weight', "Peso total não pode ser maior que 100");
        }
        if ($totalWeight && $totalWeight < 100) {
            $validator->errors()->add('full_weight', "Peso total não pode ser menor que 100");
        }
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
