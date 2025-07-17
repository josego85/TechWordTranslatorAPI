<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Translation
 */
class TranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'word_id'      => $this->word_id,
            'spanish_word' => $this->spanish_word,
            'german_word'  => $this->german_word,
        ];
    }
}