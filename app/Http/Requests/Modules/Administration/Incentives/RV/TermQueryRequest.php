<?php


namespace App\Http\Requests\Modules\Administration\Incentives\RV;

use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Traits\FailedValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TermQueryRequest extends FormRequest
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
        $username  =  (int) preg_replace('/\D/', '', $this->route('username'));

        $this->merge([
            'username' => $username
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
            'username' => 'required|integer',
        ];
    }
}
