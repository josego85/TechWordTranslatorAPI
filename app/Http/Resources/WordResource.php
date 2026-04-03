<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Translation;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Word
 */
class WordResource extends JsonResource
{
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'word' => $this->english_word,
            'categories' => $this->categories->map(fn (Category $category) => [
                'slug' => $category->slug,
                'name' => $category->name,
            ])->toArray(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'translations' => $this->translations->map(fn (Translation $translation) => [
                'language' => $translation->language,
                'translation' => $translation->translation,
            ])->toArray(),
        ];
    }
}
