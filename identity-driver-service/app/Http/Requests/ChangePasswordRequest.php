<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $passwordRules = \Illuminate\Validation\Rules\Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols();

        if (app()->environment('production')) {
            $passwordRules->uncompromised();
        }

        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', $passwordRules],
        ];
    }
}
