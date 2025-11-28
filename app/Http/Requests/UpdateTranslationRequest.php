<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'translation_id' => 'required|integer|exists:translations,id',
            'word_id' => 'sometimes|integer|exists:words,id',
            'language' => 'sometimes|string|max:10',
            'translation' => 'sometimes|string|max:255',
        ];
    }

    #[\Override]
    public function validationData()
    {
        return array_merge($this->all(), [
            'translation_id' => $this->route('translation'),
        ]);
    }

    #[\Override]
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function getTranslationId(): int
    {
        return (int) $this->route('translation');
    }
}
