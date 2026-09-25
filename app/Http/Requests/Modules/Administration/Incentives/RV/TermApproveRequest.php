<?php

namespace App\Http\Requests\Modules\Administration\Incentives\RV;

use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Modules\Administration\TermInterface;
use App\Traits\FailedValidation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TermApproveRequest extends FormRequest
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
            'status' => ['required', 'integer', 'in:' . TermInterface::STATUS_APPROVED . ',' . TermInterface::STATUS_REPROVED],
        ];
    }

    public function withValidator($validator)
    {   // O método "after" é chamado após a validação padrão das regras
        $validator->after(function ($validator) {

            $term = $this->route('term');          
           
            $dateRef = Carbon::parse($term->month_ref)->startOfMonth();
            $dateCurrent = Carbon::now()->startOfMonth();

            if ($term->status['id'] != TermInterface::STATUS_PENDING) {
                $validator->errors()->add('status', 'Só é possivel APROVAR ou REPROVAR termos com status pendente');
            }

            if (!$dateCurrent->lessThanOrEqualTo($dateRef)) {
                $validator->errors()->add('status', 'Não é possivel validar um termo fora do seu mês de referência');
            }
        });
    }
}
