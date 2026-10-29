<?php

namespace App\Http\Requests\Admin;

use App\Enums\TechnologyCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TechnologyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'category' => ['required', Rule::enum(TechnologyCategory::class)],
            'icon' => ['nullable', 'string', 'max:50', 'regex:/^(?:[a-z0-9]+|[a-z0-9]+(?:-[a-z0-9]+)*\.(?:svg|png|webp))$/'],
            'color' => ['nullable', 'hex_color', 'size:7'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
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
            'name' => '技術名',
            'category' => '分類',
            'icon' => 'アイコン名',
            'color' => 'アイコンの色',
            'sort_order' => '表示順',
        ];
    }
}
