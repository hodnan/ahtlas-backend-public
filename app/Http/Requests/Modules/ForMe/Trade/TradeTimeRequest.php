<?php

namespace App\Http\Requests\Modules\ForMe\Trade;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Modules\ForMe\Trade\TradeTime;
use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use App\Traits\FailedValidation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TradeTimeRequest extends FormRequest
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
            'time.label' => 'required',
            'time.time_start' => 'required|date_format:H:i:s',
            'time.time_end' => 'required|date_format:H:i:s',
        ];
    }

    public function messages()
    {
        return [
            'time.label' => 'É necessário escolher uma faixa de horário.',
            'time.time_start.required' => 'Horário inicial deve ser no formato data hora.',
            'time.time_end.required' => 'Horário final deve ser no formato data hora.',
        ];
    }

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {
            $user = Auth::user();
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

            if ($this->status == TradetimeInterface::STATUS_CANCELED) {
                $validator->errors()->add('is_canceled', 'Não é possivel editar uma solicitação cancelada');
            }

            if ($this->status == TradetimeInterface::STATUS_APPROVED) {
                $validator->errors()->add('is_aproved', 'Não é possivel editar uma solicitação aprovada');
            }

            $today = Carbon::now();
            $daysUpdate = $today->diffInDays($this->updated_at);
            if ($daysUpdate > 45 && $this->updated_at) {
                $validator->errors()->add('expires_in', 'Não é possivel renovação com mais de 45 dias');
            }
        });
    }
}
