<?php

namespace Netauratech\CoreCms\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UserFormRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'string'],
            'email' => ['required', 'string'],
            'new_password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => [''],
        ];

        if ($this->isMethod('post')) {
            $rules['username'][] = 'unique:users,username';
            $rules['email'][] = 'unique:users,email';
        } elseif ($this->isMethod('put')) {
            $rules['username'][] = 'unique:users,username,' . $this->route('user')->id;
            $rules['email'][] = 'unique:users,email,' . $this->route('user')->id;
        }

        return $rules;
    }
}
