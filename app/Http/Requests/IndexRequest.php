<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', 'string', 'min:1', 'max:255'],
        ];
    }

    public function getPerPage(): int
    {
        return $this->integer('per_page', 15);
    }

    public function getPage(): int
    {
        return $this->integer('page', 1);
    }

    public function getSearch(): ?string
    {
        return $this->input('search');
    }
}
