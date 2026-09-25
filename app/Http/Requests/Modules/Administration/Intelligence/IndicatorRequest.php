<?php

namespace App\Http\Requests\Modules\Administration\Intelligence;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FailedValidation;


class IndicatorRequest extends FormRequest
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
            'name' => ucfirstException($this->name),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $ruleId = $this->input('id') ? ",".$this->input('id') .",id" : null;

        return [
            'name'=>["required", "unique:App\Models\Modules\Administration\Intelligence\Indicator,name$ruleId"],
            'active'=>["required","integer"],
            'direction'=>["required","integer"],
            'calc'=>["required","integer"],
            'symbol'=>["required","integer"],           
        ];
    }

}
