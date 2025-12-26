<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\Tag;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match($this->method()) {
            'POST' => Gate::allows('create tags'),
            'PUT', 'PATCH' => Gate::allows('update', $this->route('tag')),
            'DELETE' => Gate::allows('delete', $this->route('tag')),
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
        $tagId = $this->route('tag')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                Rule::unique('tags')->ignore($tagId),
            ],
            'slug' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                Rule::unique('tags')->ignore($tagId),
                'regex:/^[a-z0-9-]+$/',
            ],
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
     * Get the validated tag data.
     *
     * @return array<string, mixed>
     */
    public function getTagData(): array
    {
        return $this->validated();
    }
}