<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            "username" => ["required", "email", "max:100"],
            "password" => ["required", Password::min(6)->letters()->numbers()->symbols()]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "username" => strtolower($this->input("username"))
        ]);
    }

    protected function passedValidation(): void
    {
        $this->merge([
            "password" => bcrypt($this->input("password"))
        ]);
    }
}
