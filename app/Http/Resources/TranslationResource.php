<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Translation;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Translation
 */
class TranslationResource extends JsonResource
{
    #[\Override]
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'word_id' => $this->word_id,
            'language' => $this->language,
            'translation' => $this->translation,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
