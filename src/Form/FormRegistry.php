<?php

namespace Netauratech\CoreCms\Form;

use Closure;
use InvalidArgumentException;
class FormRegistry
{
    /**
     * @var array An associative array storing form field definitions.
     * The key is the form name (e.g., 'content_form'),
     * the value is an associative array where the key is the field name
     * and the value is a Closure or an array of field parameters.
     * Ex: ['content_form' => ['title' => fn() => '<input type="text" name="title">']]
     */
    protected array $formFields = [];

    /**
     * @var array An associative array storing validation rules for forms.
     * The key is the form name (e.g., 'content_form'),
     * the value is an associative array where the key is the field name
     * and the value is an array of validation rules.
     * Ex: ['content_form' => ['title' => ['required', 'string', 'max:255']]]
     */
    protected array $validationRules = [];

    /**
     * Registers one or more form fields for a given form.
     *
     * @param string $formName The unique name of the form (e.g., 'content_form', 'media_form').
     * @param array $fields An array of field definitions. Each key is the field name,
     * and the value is either a Closure that returns the field's HTML,
     * or an array of parameters for the field.
     * Ex: ['title' => fn() => '<input ...>', 'slug' => ['type' => 'text', 'label' => 'Slug']]
     * @return void
     * @throws InvalidArgumentException If a field definition is not a Closure or an array.
     */
    public function registerFormFields(string $formName, array $fields): void
    {
        if (!isset($this->formFields[$formName])) {
            $this->formFields[$formName] = [];
        }

        foreach ($fields as $fieldName => $fieldDefinition) {
            if (!($fieldDefinition instanceof Closure) && !is_array($fieldDefinition)) {
                throw new InvalidArgumentException(
                    "The definition for field '{$fieldName}' in form '{$formName}' must be a Closure or an array."
                );
            }
            // Allows later registrations to override previous ones for the same field
            $this->formFields[$formName][$fieldName] = $fieldDefinition;
        }
    }

    /**
     * Registers one or more sets of validation rules for a given form.
     *
     * @param string $formName The unique name of the form.
     * @param array $rules An array of rules. Each key is the field name,
     * and the value is an array or a string of Laravel validation rules.
     * Ex: ['title' => ['required', 'string'], 'slug' => 'unique:posts']
     * @return void
     * @throws InvalidArgumentException If validation rules are not an array or a string.
     */
    public function registerValidationRules(string $formName, array $rules): void
    {
        if (!isset($this->validationRules[$formName])) {
            $this->validationRules[$formName] = [];
        }

        foreach ($rules as $fieldName => $fieldRules) {
            if (!is_array($fieldRules) && !is_string($fieldRules)) {
                throw new InvalidArgumentException(
                    "Validation rules for field '{$fieldName}' in form '{$formName}' must be an array or a string."
                );
            }
            // Merges or overrides existing rules for the same field
            // If the field already exists, new rules will replace old ones
            $this->validationRules[$formName][$fieldName] = $fieldRules;
        }
    }

    /**
     * Retrieves all form field definitions for a given form.
     *
     * @param string $formName The unique name of the form.
     * @return array An associative array of field definitions.
     */
    public function getFormFields(string $formName): array
    {
        return $this->formFields[$formName] ?? [];
    }

    /**
     * Retrieves all validation rules for a given form.
     *
     * @param string $formName The unique name of the form.
     * @return array An associative array of validation rules.
     */
    public function getValidationRules(string $formName): array
    {
        return $this->validationRules[$formName] ?? [];
    }
}