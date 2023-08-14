<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\WordService;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WordServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateWordWithTranslations()
    {
        $wordService = new WordService();
        $wordData = [
            'english_word' => 'network',
            'translations' => [
                'spanish_word' => 'red',
                'german_word' => 'netzwerk'
            ]
        ];

        $result = $wordService->createWordWithTranslations($wordData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('words', ['english_word' => 'network']);
        $this->assertDatabaseHas('translations', ['spanish_word' => 'red']);
        $this->assertDatabaseHas('translations', ['german_word' => 'netzwerk']);
    }

    public function testUpdateWordWithTranslations()
    {
        // Create a word with translations
        $word = Word::create([
            'english_word' => 'existing_network'
        ]);
        $word->translations()->create([
            'spanish_word' => 'existente',
            'german_word' => 'vorhanden'
        ]);

        $wordService = new WordService();
        $newEnglishWord = 'updated_network';
        $newTranslations = [
            'spanish_word' => 'actualizado',
            'german_word' => 'aktualisiert'
        ];

        $result = $wordService->updateWordWithTranslations(
            $word->id,
            $newEnglishWord,
            $newTranslations
        );

        $this->assertInstanceOf(Word::class, $result);
        $this->assertEquals($newEnglishWord, $result->english_word);
        $this->assertDatabaseHas('translations', ['spanish_word' => 'actualizado']);
        $this->assertDatabaseHas('translations', ['german_word' => 'aktualisiert']);
    }

    public function testDestroyWordWithTranslations()
    {
        // Create a word with translations
        $word = Word::create([
            'english_word' => 'existing_network'
        ]);
        $word->translations()->create([
            'spanish_word' => 'existente',
            'german_word' => 'vorhanden'
        ]);

        $wordService = new WordService();
        $result = $wordService->destroyWordWithTranslations($word->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('words', ['id' => $word->id]);
        $this->assertDatabaseMissing('translations', ['word_id' => $word->id]);
    }

    public function testGetAllWordsWithTranslations()
    {
        Word::create([
            'english_word' => 'network',
        ])->translations()->create([
            'spanish_word' => 'red',
            'german_word' => 'netzwerk'
        ]);

        Word::create([
            'english_word' => 'computer',
        ])->translations()->create([
            'spanish_word' => 'computadora',
            'german_word' => 'computer'
        ]);

        $wordService = new WordService();
        $result = $wordService->getAllWordsWithTranslations();

        $this->assertCount(2, $result);
        $this->assertEquals('network', $result[0]->english_word);
        $this->assertEquals('red', $result[0]->translations[0]->spanish_word);
    }

    public function testShowWordWithTranslations()
    {
        $word = Word::create([
            'english_word' => 'network',
        ]);
        $word->translations()->create([
            'spanish_word' => 'red',
            'german_word' => 'netzwerk'
        ]);

        $wordService = new WordService();
        $result = $wordService->showWordWithTranslations($word->id);

        $this->assertInstanceOf(Word::class, $result);
        $this->assertEquals('network', $result->english_word);
        $this->assertEquals('red', $result->translations[0]->spanish_word);
    }
}
