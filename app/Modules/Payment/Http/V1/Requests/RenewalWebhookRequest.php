<?php

namespace App\Modules\Payment\Http\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewalWebhookRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'plan_id' => ['required', 'numeric', 'exists:plans,id'],
            // There are more fields for validation in a real-world scenario.
        ];
    }
}
