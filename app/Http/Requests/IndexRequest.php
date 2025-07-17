<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'cursor' => 'sometimes|string',
        ];
    }

    public function getPerPage(): int
    {
        return $this->integer('per_page', 15);
    }

    public function getCursor(): ?string
    {
        return $this->input('cursor');
    }
}
