<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'english_word',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the translations for the word.
     *
     * @return HasMany<Translation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /** @return BelongsToMany<Category, $this> */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'word_category');
    }

    /**
     * Get translation for a specific language.
     */
    public function getTranslation(string $language): ?Translation
    {
        return $this->translations()->where('language', $language)->first();
    }

    /**
     * Update or create a translation for a specific language.
     */
    public function setTranslation(string $language, string $translation): Translation
    {
        return $this->translations()->updateOrCreate(
            ['language' => $language],
            ['translation' => $translation]
        );
    }

    /**
     * Scope a query to search words by English word or translations.
     *
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    protected function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function(Builder $q) use ($search) {
            $q->where('english_word', 'LIKE', "%{$search}%")
                ->orWhereHas('translations', function(Builder $translationQuery) use ($search) {
                    $translationQuery->where('translation', 'LIKE', "%{$search}%");
                });
        });
    }

    /**
     * Scope a query to filter words by category slug (used by GraphQL @scope).
     *
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    protected function scopeFilterByCategory(Builder $query, string $category): Builder
    {
        return $query->whereHas('categories', fn (Builder $q) => $q->where('slug', $category));
    }
}
