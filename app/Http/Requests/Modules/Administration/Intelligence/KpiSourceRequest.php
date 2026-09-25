<?php

namespace App\Http\Requests\Modules\Administration\Intelligence;

use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class KpiSourceRequest extends FormRequest
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
            'id' => 'integer',
            'owner' => ['required','string','min:8','regex:/^[^\s]+(\s+[^\s]+)*$/', Rule::exists('modules.employees', 'username')],
            'indicator' => ['required','integer', Rule::exists('modules.indicators', 'id')],
            'sla' => 'required|integer|max:0',
            'sectors' => 'required|array|min:1',
            'rebuild' => 'integer',
            'sectors.*.sector_n1_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('modules.employee_sectors_n1', 'id'),
                Rule::unique('modules.kpi_relateds', 'sector_n1_id')
                    ->where(function ($query) {
                        $query->where('indicator_id', $this->input('indicator'))
                            ->where('source_id', '!=', $this->input('id'));
                    }),
            ],
        ];
    }
}
