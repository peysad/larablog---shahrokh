<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $post = $this->route('post');
        
        return match($this->method()) {
            'POST' => Gate::allows('create posts'),
            'PUT', 'PATCH' => Gate::allows('update', $post),
            'DELETE' => Gate::allows('delete', $post),
            default => false,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $postId = $this->route('post')?->id;

        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'slug' => [
                'nullable',
                'string',
                'min:3',
                'max:255',
                Rule::unique('posts')->ignore($postId),
                'regex:/^[a-z0-9-]+$/',
            ],
            'excerpt' => [
                'nullable',
                'string',
                'max:500',
            ],
            'body' => [
                'required',
                'string',
                'min:10',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],
            'published_at' => [
                'nullable',
                'date',
                'after_or_equal:now',
            ],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
                'dimensions:min_width=400,min_height=300',
            ],
            'categories' => [
                'nullable',
                'array',
                'max:5',
            ],
            'categories.*' => [
                'exists:categories,id',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'exists:tags,id',
            ],
             'allow_comments' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'post title',
            'slug' => 'URL slug',
            'excerpt' => 'post excerpt',
            'body' => 'post content',
            'status' => 'publication status',
            'published_at' => 'publish date',
            'featured_image' => 'featured image',
            'categories' => 'categories',
            'categories.*' => 'category',
            'tags' => 'tags',
            'tags.*' => 'tag',
            'allow_comments' => 'comment settings',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'published_at.after_or_equal' => 'The publish date must be today or in the future.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->slug && $this->title) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title),
            ]);
        }

        if ($this->status === 'published' && !$this->published_at) {
            $this->merge([
                'published_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Get validated and processed post data.
     *
     * @return array<string, mixed>
     */
    public function getPostData(): array
    {
        $validated = $this->validated();

        // Remove image from array as it's handled in controller
        unset($validated['featured_image']);

        $post = $this->route('post'); // Get the post object if updating
        $currentUserId = $this->user()->id;

        // Handle Checkbox Logic for allow_comments
        // HTML Checkboxes only send data when CHECKED.
        // If unchecked, we must manually set it to false for the database.

        $validated['allow_comments'] = $this->has('allow_comments');
        if ($post) {
            // --- UPDATE SCENARIO ---
            
            // 1. Ensure we DO NOT overwrite the original author (user_id)
            unset($validated['user_id']);
            
            // 2. Set the updater (updated_by) to the current user
            $validated['updated_by'] = $currentUserId;

        } else {
            // --- CREATE SCENARIO ---
            
            // 1. Set the original author (user_id) to the current user
            $validated['user_id'] = $currentUserId;
        }

        return $validated;
    }
}