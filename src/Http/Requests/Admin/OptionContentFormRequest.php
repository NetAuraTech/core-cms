<?php

namespace Netauratech\CoreCms\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OptionContentFormRequest extends FormRequest
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
        $option = $this->route('option');
        $rules = [
            'key' => ['min:3'],
            'value' => [],
            'type' => [],
        ];

        if ($option && $option->used_by_cms) {
            $rules['key'][] = 'nullable';
            $rules['type'][] = 'nullable';
        }elseif ($this->isMethod('post')) {
            $rules['key'][] = 'unique:options,key';
            $rules['key'][] = 'required';
            $rules['type'][] = 'in:image,text,content,theme';
        } elseif ($this->isMethod('put')) {
            $rules['key'][] = 'unique:options,key,' . $this->route('option')->key . ',key';
            $rules['key'][] = 'required';
            $rules['type'][] = 'in:image,text,content,theme';
        }

        return $rules;
    }
}