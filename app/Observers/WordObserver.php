<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Word;
use Illuminate\Support\Facades\Log;

class WordObserver
{
    public function created(Word $word): void
    {
        Log::info('Word created', [
            'word_id'      => $word->id,
            'english_word' => $word->english_word,
            'ip'           => request()->ip() ?? 'cli',
        ]);
    }

    public function updated(Word $word): void
    {
        Log::info('Word updated', [
            'word_id'      => $word->id,
            'english_word' => $word->english_word,
            'ip'           => request()->ip() ?? 'cli',
        ]);
    }

    public function deleted(Word $word): void
    {
        Log::warning('Word deleted', [
            'word_id' => $word->id,
            'ip'      => request()->ip() ?? 'cli',
        ]);
    }
}
