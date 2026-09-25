<?php

namespace App\Http\Requests\Modules\Management\ControlCenter;

use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class ControlCenterTrackingStageRequest extends FormRequest
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
            'id' => ['required', 'integer'],
            'status' => ['required', 'integer'],
        ];
    }



    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {
            // Capturar o modelo ControlCenterTrackingStage da rota
            $stage = $this->route('stage');
            $now = Carbon::now()->format('Y-m-d');

            if ($stage) {
                // Adicione regras ou valide algo com o modelo aqui
                if ($stage->status['id'] == ControlCenterInterface::STAGE_STATUS_VALIDATED) {
                    $validator->errors()->add('status', 'Não é possivel editar stage [Validado]');
                }

                if ($now != $stage->created_at) {
                    $validator->errors()->add('status', 'Não é possivel editar stage fora da data de criação');
                }
            }
        });
    }
}
