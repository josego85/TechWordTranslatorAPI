<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'word' => ['required', 'integer', 'min:1', 'exists:words,id'],
            'english_word' => ['required', 'string', 'max:255'],
        ];
    }

    #[\Override]
    public function validationData()
    {
        return array_merge($this->all(), [
            'word' => $this->route('word'),
        ]);
    }

    #[\Override]
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function getWordId(): int
    {
        return (int) $this->route('word');
    }
}
