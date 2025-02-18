<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class RolesStoreRequest extends FormRequest
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
        return [
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|exists:permissions,name',
            'role' => 'required|unique:roles,name|max:60',
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You are not authorized to perform this action.');
    }
}
