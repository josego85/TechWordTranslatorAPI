<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * @var array<int, string>
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
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Get translation for a specific language.
     *
     * @param  string  $language
     * @return Translation|null
     */
    public function getTranslation(string $language): ?Translation
    {
        return $this->translations()->where('language', $language)->first();
    }

    /**
     * Update or create a translation for a specific language.
     *
     * @param  string  $language
     * @param  string  $translation
     * @return Translation
     */
    public function setTranslation(string $language, string $translation): Translation
    {
        return $this->translations()->updateOrCreate(
            ['language' => $language],
            ['translation' => $translation]
        );
    }
}
