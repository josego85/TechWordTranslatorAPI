<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    /** @var list<string> */
    protected $fillable = ['slug', 'name'];

    /** @return BelongsToMany<Word, $this> */
    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'word_category');
    }
}
