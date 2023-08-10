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
}
