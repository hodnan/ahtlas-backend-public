<?php

namespace App\Http\Requests\Modules\TacticalCenter\Bulletin;


use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class BackofficeRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {

        $filter = filter_var($this->filter,  FILTER_VALIDATE_BOOLEAN);

        $manager = !$filter ? Auth::user()->manager_n1_id : $this->manager;

        $this->merge([
            'manager' => $manager ,            
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateDate($validator);
            $this->validateUsername($validator);
        });
    }

    public function validateDate($validator)
    {
        $dateStart =  Carbon::parse($this->date_start);
        $dateEnd =  Carbon::parse($this->date_end);

        if ( $dateStart->diffInDays($dateEnd) > 5) {
            $validator->errors()->add('date_start', 'Período não pode ser superior a 5 dias');
            $validator->errors()->add('date_end', '');
        }

        if (strtotime($this->date_end) < strtotime($this->date_start)) {
            $validator->errors()->add('date_start', 'O [Fim] deve ser maior ou igual a [Início].');
            $validator->errors()->add('date_end', '');
        }
    }

    public function validateUsername($validator)
    {
        $filtersProvided = $this->filled('manager') 
        || $this->filled('employee') 
        || $this->filled('sector')  
        || $this->filled('mailing') 
        || $this->filled('queue') 
        || $this->filled('environment') 
        ;

        if (!$filtersProvided) {
            $validator->errors()->add('manager', 'É necessário fornecer ao menos um dos filtros: [Gestor], [Colaborador], [Setor], [Mailing], [Fila] ou [Ambiente] ');
        }
    }
}
