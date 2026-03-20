<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Translation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('write', Translation::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'translation_id' => ['required', 'integer', 'exists:translations,id'],
            'word_id' => ['sometimes', 'integer', 'exists:words,id'],
            'language' => ['sometimes', Rule::string()->max(10)],
            'translation' => ['sometimes', Rule::string()->max(255)],
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
