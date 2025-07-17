<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'word_id',
        'spanish_word',
        'german_word',
    ];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }
}
