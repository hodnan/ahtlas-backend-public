<?php

namespace App\Http\Requests\Modules\ForMe\Trade;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Modules\ForMe\Trade\TradeTime;
use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;

class TradeTimeUpdateRequest extends FormRequest
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
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'status' => "'Status' é um campo obrigatório",
        ];
    }

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {
            $oldReg = TradeTime::where('id', $this->id)->first();
            $user = auth()->user();
            $tradetime = TradeTime::where('username', $user->username)
                ->whereIn('status', [TradetimeInterface::STATUS_PENDING, TradetimeInterface::STATUS_RENOVATED])
                ->where(function ($query) {
                    if ($this->id) {
                        $query->where('id', '<>', $this->id);
                    }
                })
                ->count();
            if ($tradetime > 0) {
                $validator->errors()->add('just_one_request', 'Já existe uma solicitação em andamento.');
            }

            if (!$user->is_veteran) {
                $validator->errors()->add('is_veteran', 'Solicitação de troca, somente colaboradores com mais de 90 dias.');
            }

            if ($this->status == TradetimeInterface::STATUS_APPROVED) {
                $validator->errors()->add('status', "'Status' não pode ser 'Concluído'.");
            }
            if ($this->status == TradetimeInterface::STATUS_PENDING) {
                $validator->errors()->add('status', "'Status' não pode ser pendente");
            }

            if ($oldReg->status == TradetimeInterface::STATUS_CANCELED) {
                $validator->errors()->add('is_canceled', 'Não é possivel editar uma solicitação cancelada');
            }

            if ($oldReg->status == TradetimeInterface::STATUS_APPROVED) {
                $validator->errors()->add('is_aproved', 'Não é possivel editar uma solicitação concluída');
            }

            $today = Carbon::now();
            $daysUpdate = $today->diffInDays($this->updated_at);
            if ($daysUpdate > 45 && $this->updated_at) {
                $validator->errors()->add('expires_in', 'Não é possivel renovação com mais de 45 dias');
            }
        });
    }
}
