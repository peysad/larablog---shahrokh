<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\Category;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match($this->method()) {
            'POST' => Gate::allows('create categories'),
            'PUT', 'PATCH' => Gate::allows('update', $this->route('category')),
            'DELETE' => Gate::allows('delete', $this->route('category')),
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
        $categoryId = $this->route('category')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
                Rule::unique('categories')->ignore($categoryId),
                'regex:/^[a-z0-9-]+$/',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
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
            'name' => 'category name',
            'slug' => 'category slug',
            'description' => 'category description',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name),
            ]);
        }
    }

    /**
     * Get the validated category data.
     *
     * @return array<string, mixed>
     */
    public function getCategoryData(): array
    {
        return $this->validated();
    }
}