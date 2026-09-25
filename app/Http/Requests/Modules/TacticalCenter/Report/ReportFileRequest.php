<?php

namespace App\Http\Requests\Modules\TacticalCenter\Report;

use App\Models\Modules\TacticalCenter\Report\Report;
use App\Models\Modules\TacticalCenter\Report\ReportFile;
use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ReportFileRequest extends FormRequest
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
            'month_ref' => strlen($this->month_ref ) >= 7 ? Carbon::parse($this->month_ref) : $this->month_ref,
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
            'report_uuid' => 'required|uuid',           
            'month_ref' => 'required|date',           
            'file' => 'required|mimes:xlsx,xlsb,xls,txt,csv',
        ];
    }

    public function withValidator($validator)
    {        
         $validator->after(function ($validator) {

            if (!$validator->errors()->has('report_uuid')) {
                $report = Report::where('uuid', $this->report_uuid)
                                ->whereIn('type', [ReportInterface::REPORT_TYPE_EXCEL, ReportInterface::REPORT_TYPE_MAILING])
                                ->first();
    
                if (!$report) {
                    $validator->errors()->add('file', "Esse relatório nãoo existe ou não é do tipo Excel ou Mailing.");
                }
            }

            if ($this->hasFile('file') && is_array($this->file('file'))  ) {
                if (count($this->file('file')) > 1)
                {
                    $validator->errors()->add('file', 'Apenas um arquivo pode ser enviado.');
                }
            }
        });
    }
}
