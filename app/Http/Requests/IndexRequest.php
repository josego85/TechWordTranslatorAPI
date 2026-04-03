<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', Rule::string()->min(1)->max(255)],
            'category' => ['sometimes', Rule::string()->min(1)->max(50)],
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
        return $this->string('search')->toString() ?: null;
    }

    public function getCategory(): ?string
    {
        return $this->string('category')->toString() ?: null;
    }
}
