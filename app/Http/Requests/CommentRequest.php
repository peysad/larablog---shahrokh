<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Guests can create comments if allowed
        if ($this->method() === 'POST' && !auth()->check()) {
            return config('blog.allow_guest_comments', true);
        }

        return match($this->method()) {
            'POST' => true, // Authorization checked in controller for guests
            'PUT', 'PATCH' => Gate::allows('update', $this->route('comment')),
            'DELETE' => Gate::allows('delete', $this->route('comment')),
            default => false,
        };
    }

    /**
     * Get validation rules for comments.
     */
    public function rules(): array
    {
        $rules = [
            'body' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
            'parent_id' => [
                'nullable',
                'exists:comments,id',
                Rule::prohibitedIf($this->has('commentable_id')), // Prevent nesting errors
            ],
        ];

        // Guest comment rules
        if (!auth()->check()) {
            $rules['guest_name'] = [
                'required',
                'string',
                'min:2',
                'max:50',
            ];
            $rules['guest_email'] = [
                'required',
                'email',
                'max:255',
            ];
        }

        return $rules;
    }

    /**
     * Get comment data with IP and user agent.
     */
    public function getCommentData(): array
    {
        $data = $this->validated();
        
        $data['ip_address'] = $this->ip();
        $data['user_agent'] = $this->userAgent();
        $data['user_id'] = auth()->id();
        
        $data['approved'] = false;

        if (auth()->check() && auth()->user()->hasRole(['Admin', 'Editor'])) {
            $data['approved'] = true;
        }

        return $data;
    }

    /**
     * Get validation messages.
     */
    public function messages(): array
    {
        return [
            'body.min' => 'Comment must be at least 10 characters.',
            'guest_name.required' => 'Please provide your name to comment.',
            'guest_email.required' => 'Please provide your email address.',
        ];
    }
}