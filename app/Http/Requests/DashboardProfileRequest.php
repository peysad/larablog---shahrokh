<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules.
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'avatar' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                //'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'delete_avatar' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attributes for error messages.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'bio' => 'biography',
            'avatar' => 'profile picture',
        ];
    }
}