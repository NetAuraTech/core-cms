<?php

namespace Netauratech\CoreCms\Contracts;

use Closure;

interface FormExtensionProviderInterface
{
    /**
     * Returns an array of form field definitions for a given form identifier.
     * These fields will be rendered by the consumer.
     *
     * @param string $formIdentifier The unique identifier of the form to which the fields apply (e.g., ‘content_form’, ‘user_form’).
     * @param object|null $model The model instance currently being processed by the form (may be null for creation).
     * @return array<string, Closure(object|null): string> An associative array where the key is the field name and the value is a Closure
     * which, when called, returns the HTML for the field. The Closure receives the model instance.
     */
    public function getFormFields(string $formIdentifier, object $model = null): array;

    /**
     * Returns an array of validation rules for a given Form Request class.
     * These rules will be merged with the existing rules of the Form Request.
     *
     * @param string $formRequestClass The fully qualified class name of the FormRequest (e.g., MyFormRequest::class).
     * @return array<string, array|string> Associative array where the key is the field name and the value is one or more validation rules.
     */
    public function getValidationRules(string $formRequestClass): array;
}