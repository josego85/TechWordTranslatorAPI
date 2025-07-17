<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Word
 */
class WordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'english_word' => $this->english_word,
        ];
    }
}
