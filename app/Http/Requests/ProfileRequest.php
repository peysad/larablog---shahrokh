<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
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
                Rule::unique('users')->ignore($userId),
            ],
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'social_links' => [
                'nullable',
                'array',
            ],
            'social_links.twitter' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.github' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.linkedin' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.website' => [
                'nullable',
                'url',
                'max:255',
            ],
            'avatar' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', // 2MB for avatars
                //'dimensions:min_width=100,min_height=100,max_width:2000,max_height:2000',
            ],
            'delete_avatar' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attributes.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'bio' => 'biography',
            'social_links' => 'social links',
            'avatar' => 'profile picture',
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean social links array
        $socialLinks = $this->input('social_links', []);
        $this->merge([
            'social_links' => array_filter($socialLinks ?? []),
        ]);
    }

    /**
     * Get validated profile data.
     */
    public function getProfileData(): array
    {
        $data = $this->validated();
        
        // Remove avatar fields for separate handling
        unset($data['avatar'], $data['delete_avatar']);

        return $data;
    }
}