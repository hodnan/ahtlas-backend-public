<?php

namespace App\Http\Requests\Modules\People\Employee;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeCurrentRequest extends FormRequest
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
        $this->merge([
            'manager' => !$this->has('manager') && !$this->has('filter') ? Auth::user()->manager_n1_id : $this->manager,
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
            'manager' => 'nullable',
            'username' => 'nullable|min:3',
            'name' => 'nullable|min:4',
            'sector_n1_id' => 'nullable',
        ];
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $filtersProvided = $this->filled('manager') || $this->filled('username') | $this->filled('name') || $this->filled('sector_n1_id');    
            if (!$filtersProvided) {
                $validator->errors()->add('filters', 'É necessário fornecer ao menos um dos filtros: [Matrícula], [Nome], [Setor] ou [Gestor]');
            }
        });
    }
}
