<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRoleRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('Admin');
    }

    public function rules()
    {
        return [
            'role' => ['required', 'string', Rule::in(['User', 'Author', 'Editor', 'Admin'])],
        ];
    }
}