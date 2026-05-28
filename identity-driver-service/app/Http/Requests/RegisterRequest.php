<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', $passwordRules],
            'role' => ['sometimes', 'string', 'in:PASSENGER,DRIVER,ADMIN'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
