<?php


namespace App\Http\Requests\Modules\Administration\Incentives\RV;

use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Traits\FailedValidation;
use Illuminate\Support\Facades\DB;

class TermRequest extends FormRequest
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
            'apprentice' => filter_var($this->input('apprentice'), FILTER_VALIDATE_BOOLEAN),
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
            'id' => 'nullable|integer',
            'owner' => 'required',
            'month_ref' => [
                'required',
                'date'
            ],
            'sector_n1_id' => [
                'required',
                'integer',
            ],
            'sector_n2_id' => 'required|integer',
            'campaign' => 'required',
            'payment_id' => 'required|integer',
            'apprentice' => 'required|boolean',
            'position' => 'required',
            'level' => 'required',
            'roof' => 'nullable|numeric|min:50',

            'basket' => 'required|array|min:1',
            'basket.*.indicator_id' => 'required|integer',
            'basket.*.range' => 'required|nullable|integer',
            'basket.*.operation' => 'required',
            'basket.*.calc' => 'required|array',
            'basket.*.target' => 'required|numeric',
            'basket.*.value' => 'required|numeric',

            'accelerator' => 'array',
            'accelerator.*.indicator_id' => 'required|integer',
            'accelerator.*.range' => 'required|nullable|integer',
            'accelerator.*.operation' => 'required',
            'accelerator.*.calc' => 'required|array',
            'accelerator.*.target' => 'required|numeric',
            'accelerator.*.value' => 'required|numeric',

            'deflator' => 'array',
            'deflator.*.indicator_id' => 'required|integer',
            'deflator.*.range' => 'required|nullable|integer',
            'deflator.*.operation' => 'required',
            'deflator.*.calc' => 'required|array',
            'deflator.*.target' => 'required|numeric',
            'deflator.*.value' => 'required|numeric',

            'elimination' => 'array',
            'elimination.*.indicator_id' => 'required|integer',
            'elimination.*.operation' => 'required',
            'elimination.*.calc' => 'required|array',
            'elimination.*.target' => 'required|numeric',
            'elimination.*.target' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'id.integer' =>  "'ID' deve ser um inteiro.",
            'owner.required' => "O campo 'Responsável' é obrigatório.",
            'month_ref.required' => "O campo 'Referência' é obrigatório.",
            'month_ref.date' => "O campo 'Referência' deve ser uma data válida no formato yyyy-mm-dd.",
            'sector_n1_id.required' => "O campo 'Setor' é obrigatório.",
            'sector_n1_id.integer' => "O campo 'Setor' deve ser um inteiro.",
            'sector_n2_id.integer' => "O campo 'Sub-setor' deve ser um inteiro.",
            'sector_n2_id.required' => "O campo 'Sub-setor' é obrigatório.",

            'campaign.required' =>  "O campo 'Campanha' é obrigatório.",

            'apprentice.required' =>  "O campo 'Jovem' é obrigatório.",
            'apprentice.boolen' =>  "O campo 'Jovem' deve ser booblen.",
            'payment_id.required' =>  "O campo 'Pagamento' é obrigatório.",
            'payment_id.integer' =>  "O campo 'Pagamento' deve ser um inteiro.",
            'position.required' =>  "O campo 'Tipo' é obrigatório.",
            'level.required' =>  "O campo 'Tempo' é obrigatório.",
            'roof.numeric' =>  "O campo 'Teto' deve ser um número.",
            'roof.min' =>  "O campo 'Teto' deve maior que R$ 300.",

            'basket' => "O campo 'Cesta' é obrigatório.",
            'basket.*.indicator_id.required' => "O campo 'Indicador' é obrigatório para cesta.",
            'basket.*.range.required' => "O campo 'Faixa' é obrigatório  para cesta.",
            'basket.*.range.integer' => "O campo 'Faixa' deve ser um inteiro para cesta.",
            'basket.*.calc' => "O campo 'Calculo' é obrigatório  para cesta.",
            'basket.*.operation.required' => "O campo 'Condição' é obrigatório  para cesta.",
            'basket.*.target.required' => "O campo 'Meta' é obrigatório  para cesta.",
            'basket.*.value.required' =>  "O campo 'Valor' é obrigatório  para cesta.",

            'accelerator.*.indicator_id.required' => "O campo 'Indicador' é obrigatório para acelerador.",
            'accelerator.*.operation.required' => "O campo 'Condição' é obrigatóriopara acelerador.",
            'accelerator.*.calc' => "O campo 'Calculo' é obrigatório  para acelerador.",
            'accelerator.*.target.required' => "O campo 'Meta' é obrigatório para acelerador.",
            'accelerator.*.value.required' =>  "O campo 'Valor' é obrigatório para acelerador.",

            'deflator.*.indicator_id.required' => "O campo 'Indicador' é obrigatório para deflator.",
            'deflator.*.operation.required' => "O campo 'Condição' é obrigatório para deflator.",
            'deflator.*.calc' => "O campo 'Calculo' é obrigatório  para deflator.",
            'deflator.*.target.required' => "O campo 'Meta' é obrigatório para deflator.",
            'deflator.*.value.required' =>  "O campo 'Valor' é obrigatório para deflator.",

            'elimination.*.indicator_id.required' => "O campo 'Indicador' é obrigatório para eliminatório.",
            'elimination.*.operation.required' => "O campo 'Condição' é obrigatório para eliminatório.",
            'elimination.*.calc' => "O campo 'Calculo' é obrigatório  para eliminatório.",
            'elimination.*.target.required' =>  "O campo 'Meta' é obrigatório para eliminatório.",
        ];
    }

    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {
            $this->validateNewVersion($validator);
            $this->ValidateMaxDate($validator);
            $this->ValidateCanceled($validator);

            if ($this->id) {
                $term = TermService::getTermById($this->id);
                return Redirect::back()->with('data', $term);
            }
        });
    }

    protected function validateNewVersion($validator)
    {
        $newVersion = (int) $this->newVersion ?? 0;
        // Se $this->newVerson for verdadeiro possibilita a criação de nova versão 

        $exist = Term::where('month_ref', $this->month_ref . '-01')
            ->where('sector_n1_id', $this->sector_n1_id)
            ->where('sector_n2_id', $this->sector_n2_id)
            ->where('position', $this->position)
            ->where('level', $this->level)
            ->where('campaign', $this->campaign)
            ->where('status', '<>',  TermInterface::STATUS_CANCELED)
            ->where(function($query){
                if($this->id){
                    $query->where('id', '<>' ,$this->id );
                }
            })
            ->count();

        if ($exist && !$newVersion) {

            $validator->errors()->add('newVersion', "Já existe termo com esses parametros");
        }
    }

    // Evita edição ou criação após o dia 15
    protected function ValidateMaxDate($validator)
    {
        // // // Obtém a data atual
        // $dataAtual = Carbon::now()->startOfDay();
        // $monthRef = Carbon::parse($this->month_ref)->addDay(8);

        // // Verifica se o dia do mês é maior que 9
        // if ( $this->id && $dataAtual->gt($monthRef) ) {
        //     $validator->errors()->add('data_max', "Não é possivel criar ou editar termos após o dia 9");
        // }
    }

    // Evita edição de termos com status 
    protected function ValidateCanceled($validator)
    {

        if ($this->id) {
            $term =  Term::find($this->id);

            $status = (int) $this->status;

            if ($term->status['id'] != TermInterface::STATUS_STANDBY && $term->status['id']  != TermInterface::STATUS_REPROVED) {
                $validator->errors()->add('status', "Só é possivel editar termos com status Stanby ou Reprovado ");
            }

            if ($status != TermInterface::STATUS_STANDBY && $status != TermInterface::STATUS_PENDING && $status != TermInterface::STATUS_CANCELED) {
                $validator->errors()->add('status', "Só é possivel mudar para os status Stanby, Cancelado ou Pendente");
            }
        }
    }
}
