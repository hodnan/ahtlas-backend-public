<?php


namespace App\Http\Requests\Modules\Administration\Incentives\Bonus;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Traits\FailedValidation;
use Carbon\Carbon;

class FiscalYearRequest extends FormRequest
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
            'year' => $this->input('month_start') ? Carbon::parse($this->input('month_start'))->format('Y') : null,
            'month_start' => $this->input('month_start') ? Carbon::parse($this->input('month_start'))->format('Y-m-d') : null,
            'month_end' => $this->input('month_end') ? Carbon::parse($this->input('month_end'))->format('Y-m-d') : null,
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
            'year' => [
                'required',
                'integer',
                'digits:4',
                'min:' . date('Y'),
                'max:' . (date('Y') + 1),
                Rule::unique('modules.bonus_fiscal_years', 'year')
                ->where('month_start', $this->input('month_start'))
                ->where('month_end', $this->input('month_end'))
                ->where(function ($query) {
                    if($this->input('public_id')){
                        $query->where('public_id', '!=', $this->input('public_id'));
                    }
                })
            ],
            'month_start' => 'required|date',
            'month_end' => 'required|date',
            'active' => 'required|integer',
        ];
    }


    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $year = $this->input('year');
            $monthStart = Carbon::parse($this->input('month_start'));
            $monthEnd = Carbon::parse($this->input('month_end'));

            if ($monthStart->year != $year || $monthEnd->year != $year) {
                $validator->errors()->add('year', 'Início e fim devem ser dentro do mesmo ano.');
            }
        });

        $validator->after(function ($validator) {
            if (strtotime($this->month_end) < strtotime($this->month_start)) {
                $validator->errors()->add('month_end', 'O [Fim] deve ser maior que [Início].');
            }
        });
    }
}
