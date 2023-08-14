<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table
     * 
     * @var string
     */
    protected $primaryKey = "id";

    protected $fillable = [
        'english_word',
    ];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    public function updateAttributes($attributes)
    {
        $this->fill($attributes)->save();
    }

    public function updateTranslations($translations)
    {
        $spanish_word = $translations['spanish_word'] ?? null;
        $german_word = $translations['german_word'] ?? null;

        if ($spanish_word !== null) {
            $this->translations()->update(['spanish_word' => $spanish_word]);
        }

        if ($german_word !== null) {
            $this->translations()->update(['german_word' => $german_word]);
        }
    }
}
