<?php

namespace Netauratech\CoreCms\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Netauratech\CoreCms\Form\FormRegistry;

class ContentFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $content = $this->route('content') ?? null;
        $contentId = $content ? $content->id : null;
        $contentType = $content ? $content->type : $this->route('type');

        $staticRules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('contents')->ignore($contentId),
            ],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['page', 'template', $contentType])],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
        ];

        $dynamicRules = [];
        $formRegistry = app(FormRegistry::class);

        $validationRules = [
            ...$formRegistry->getValidationRules("content_form_$contentType"),
            ...$formRegistry->getValidationRules("content_form")
        ];

        foreach ($validationRules as $key => $value) {
            $dynamicRules[$key] = $value;
        }

        return array_merge($staticRules, $dynamicRules);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('slug') === null) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }

        if ($this->route('type') && $this->input('type') === null) {
            $this->merge(['type' => $this->route('type')]);
        }
    }
}