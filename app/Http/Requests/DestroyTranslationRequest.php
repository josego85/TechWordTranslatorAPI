<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Translation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestroyTranslationRequest extends FormRequest
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
        return [];
    }

    #[\Override]
    protected function failedAuthorization(): never
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Forbidden',
        ], 403));
    }
}
