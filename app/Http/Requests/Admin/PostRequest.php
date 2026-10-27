<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,png,gif,webp', 'max:2048'],
            'remove_thumbnail' => ['boolean'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'tags' => ['array'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')],
            'slug' => [
                'required',
                'string',
                'max:30',
                'alpha_dash:ascii',
                Rule::unique(Post::class)->ignore($this->route('post')),
            ],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'published_at' => ['required', 'date'],
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
            'tags.*' => 'タグ',
        ];
    }
}
