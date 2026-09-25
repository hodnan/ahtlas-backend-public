<?php

namespace App\Http\Requests\Modules\Management\ControlCenter;


use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;


class ControlCenterTrackingRequest extends FormRequest
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
            'month_ref' => Carbon::parse($this->input('month_ref'))->format('Y-m-d'),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $kpi = $this->route('tracking');

        $id = $kpi ? $kpi->id : 0;

        return [
            'month_ref' => [
                'required',
                'date',
                Rule::unique('modules.control_center_trackings', 'month_ref')
                    ->where(function ($query) {
                        $query->where('sector_n1_id', $this->input('sector_n1_id'))
                            ->where('indicator_id', $this->input('indicator_id'));
                    })
                    ->ignore($id)
            ],
            'sector_n1_id' => ['required', 'integer'],
            'indicator_id' => ['required', 'integer'],
            'notify_level' => ['required', 'integer'],
            'goal' => ['required', 'numeric'],
            'bypass' => ['required', 'numeric'],
            'q1' => ['required', 'numeric'],
            'q2' => ['required', 'numeric'],
            'q3' => ['required', 'numeric'],
            'q4' => ['required', 'numeric'],
            'daily_goals' => ['required', 'array', 'min:28'],
            'daily_goals.*.month_ref' => ['required', 'date'],
            'daily_goals.*.date_ref' => ['required', 'date'],
            'daily_goals.*.goal' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'month_ref.unique' => __('validation.unique', ['attribute' => __('validation.attributes.tracking')]),
        ];
    }

    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {

            $tracking = $this->route('tracking');
           if($tracking){
            $month_ref = Carbon::parse($tracking->month_ref)->startOfMonth();
            $dateCurrent = Carbon::now()->startOfMonth();

          
            if (!$dateCurrent->lessThanOrEqualTo($month_ref)) {
                $validator->errors()->add('status', 'Não é possivel editar acompanhamento de meses anteriores');
            }
           }
           
        });
    }
}
