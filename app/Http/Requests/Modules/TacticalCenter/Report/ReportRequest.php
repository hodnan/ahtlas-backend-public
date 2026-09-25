<?php

namespace App\Http\Requests\Modules\TacticalCenter\Report;

use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
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
            'title' => ucfirstException($this->title),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $uuid = $this->route('report');

        $rules = [
            'title' => [
                "required",
                Rule::unique('modules.reports', 'title')
                    ->where(function ($query) {
                        return $query->where('type', $this->input('type'));
                    })
                    ->ignore($uuid, 'uuid'),
            ],
            'type' => 'integer',
            'group' => 'required|integer',
            'schedule_time' => 'required|date_format:H:i',
            'atd' => 'required|date_format:H:i',
            'interval_type' => 'required|integer',
            'interval_values' => 'required|array|max:5|min:1',
            'interval_values.*' => 'required',
            'owners' => 'required|array|max:3',
            'owners.*' => 'required',
            'active' => 'required|integer',
            'url' => 'nullable|url',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {

        $validator->sometimes('type', 'required', function ($input) {
            return !$this->route('report');
        });

        $validator->sometimes('url', 'required', function ($input) {
            return $input->type == ReportInterface::REPORT_TYPE_DASH && !$this->route('report');
        });

        $validator->sometimes('file', 'required', function ($input) {
            return $input->type == ReportInterface::REPORT_TYPE_CUBE && !$this->route('report');
        });


        $validator->sometimes('url', 'prohibited', function ($input) {
            return $input->type == ReportInterface::REPORT_TYPE_CUBE;
        });

        $validator->sometimes('file', 'prohibited', function ($input) {
            return $input->type == ReportInterface::REPORT_TYPE_DASH;
        });


        // Adiciona a validação personalizada para garantir que apenas um arquivo foi enviado
        $validator->after(function ($validator) {
            if ($this->hasFile('file') && $this->hasFile('file') == ReportInterface::REPORT_TYPE_CUBE) {
                $file = $this->file('file');

                if ($file->getClientOriginalExtension() != 'xlsb') {
                    $validator->errors()->add('file', 'Apenas um arquivo xlsb.');
                }
            }
            if ($this->hasFile('file') && is_array($this->file('file'))) {

                if (count($this->file('file')) > 1) {
                    $validator->errors()->add('file', 'Apenas um arquivo pode ser enviado.');
                }
            }
        });
    }

   
}
