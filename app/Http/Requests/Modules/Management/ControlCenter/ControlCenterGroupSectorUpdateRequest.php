<?php

namespace App\Http\Requests\Modules\Management\ControlCenter;

use App\Models\Modules\Management\ControlCenter\ControlCenterGroupSector;
use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;


class ControlCenterGroupSectorUpdateRequest extends FormRequest
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
            'active' => filter_var($this->input('active'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('group');

        return [
            'active' => [
                'required',
                'boolean'
            ]
        ];
    }

    protected function withValidator($validator)
    {
        $group = $this->route('group'); // ID do registro atual

      
        $validator->after(function ($validator) use ($group) {
            // Verifica se o valor de 'active' é verdadeiro
            if ($this->input('active') ) {             

                $exists = ControlCenterGroupSector::where('active', 1)->where('name', ucfirstException($group->name))->exists();

                // Se já existir, adiciona um erro de validação
                if ($exists) {
                    $validator->errors()->add('name', __('validation.unique', ['attribute' => __('validation.attributes.name')]));
                }
            }
        });
    }
}
