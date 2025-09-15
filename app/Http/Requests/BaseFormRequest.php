<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\MultilingualValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    use MultilingualValidationTrait;
    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    /**
     * Prepare the data for validation and set the locale.
     */
    protected function prepareForValidation(): void
    {
        $this->setAppLocale();
        parent::prepareForValidation();
    }

    /**
     * Set the application locale based on the Language header.
     */
    protected function setAppLocale(): void
    {
        $language = $this->header('Language', 'en');

        // Ensure only supported languages are set
        $supportedLanguages = ['en', 'lt'];
        if (in_array($language, $supportedLanguages)) {
            app()->setLocale($language);
        }
    }

    /**
     * Get custom validation messages with language support.
     * Child classes can override this method to provide specific messages.
     */
    public function messages(): array
    {
        return $this->getLocalizedMessages();
    }

    /**
     * Get localized validation messages.
     * This method can be overridden in child classes for custom messages.
     */
    protected function getLocalizedMessages(): array
    {
        return [];
    }
}