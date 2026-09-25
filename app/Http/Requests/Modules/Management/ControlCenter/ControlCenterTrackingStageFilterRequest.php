<?php

namespace App\Http\Requests\Modules\Management\ControlCenter;

use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Carbon\Carbon;

class ControlCenterTrackingStageFilterRequest extends FormRequest
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
            'month_ref' => $this->has('month_ref')  && $this->month_ref ? Carbon::parse($this->month_ref) : null,
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
            'month_ref' => 'nullable|date',
            'created_at' => 'nullable|date',            
        ];
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $filtersProvided = $this->filled('month_ref') || $this->filled('created_at');    
            if (!$filtersProvided) {
                $validator->errors()->add('filters', 'É necessário fornecer ao menos um dos filtros: [Mês de referência] ou [Data envio]');
            }
        });
    }

  

   
}
