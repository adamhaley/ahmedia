<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChatLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'question' => ['required', 'string', 'max:500'],
            // Honeypot: a real visitor never sees or fills this field (hidden
            // via CSS in the widget markup), so any non-empty value here is a
            // strong bot signal. Silently rejecting via validation, rather
            // than a distinct error message, avoids teaching a bot which
            // field to leave blank.
            'website' => ['prohibited'],
        ];
    }
}
