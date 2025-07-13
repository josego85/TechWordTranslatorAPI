<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Word
 */
class WordResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var \App\Models\Translation|null $translation */
        $translation = $this->translations->first();

        return [
            'id'        => $this->id,
            'word'      => $this->english_word,
            'locale'    => [
                'es' => $translation?->spanish_word,
                'de' => $translation?->german_word,
            ],
        ];
    }
}