<?php

namespace App\Http\Requests\Modules\Administration\Planning;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;

class PlanningFileLoadRequest extends FormRequest
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
            'month_ref' => $this->input('month_ref') ? Carbon::parse($this->input('month_ref'))->format('Y-m-d') : null,
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

        return [
            'type' => 'required|integer',
            'month_ref' => ['required', 'date'],
            'file' => 'required|file|mimes:csv,txt',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateFileType($validator);
        });
    }

    public function validateFileType($validator)
    {
         $file = $this->file('file');
            if ($file) {
                // Verifica o tipo MIME real
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file->getPathname());
                finfo_close($finfo);

                $allowedMimeTypes = ['text/csv', 'text/plain'];
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    $validator->errors()->add('file', 'O arquivo deve ser um CSV ou TXT válido.');
                }

                // Verifica a extensão
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, ['csv', 'txt'])) {
                    $validator->errors()->add('file', 'A extensão do arquivo deve ser .csv ou .txt.');
                }
            }
    }

}
