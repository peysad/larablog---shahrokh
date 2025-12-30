<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserBanRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('Admin');
    }

    public function rules()
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }
}