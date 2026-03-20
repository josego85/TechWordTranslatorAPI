<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Word;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestroyWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('write', Word::class);
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
