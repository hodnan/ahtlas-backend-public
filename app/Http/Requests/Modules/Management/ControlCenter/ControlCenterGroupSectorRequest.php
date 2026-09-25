<?php

namespace App\Http\Requests\Modules\Management\ControlCenter;

use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;


class ControlCenterGroupSectorRequest extends FormRequest
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
        return [          
            'name' => ['required', 'string',
            Rule::unique('modules.control_center_group_sectors', 'name')->where('active',1)
        ],
            'sectors'=> 'required|array|min:2',
            'sectors.*.sector_n1_id' => [
                'required',
                'integer',
                'distinct'
            ],
        ];
    }
}
