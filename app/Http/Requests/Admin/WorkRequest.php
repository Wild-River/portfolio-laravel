<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Models\Work;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'demo_url' => ['required', 'url:http,https', 'max:255'],
            'github_url' => ['nullable', 'url:http,https', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,png,gif,webp', 'max:2048'],
            'remove_thumbnail' => ['boolean'],
            'images' => ['array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,png,gif,webp', 'max:2048'],
            'delete_images' => ['array'],
            'delete_images.*' => ['integer'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'tags' => ['array'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')],
            'slug' => [
                'required',
                'string',
                'max:30',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(Work::class)->ignore($this->route('work')),
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
            'images.*' => 'ギャラリー画像',
        ];
    }
}
