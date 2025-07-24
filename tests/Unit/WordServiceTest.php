<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\TranslationException;
use App\Exceptions\WordNotFoundException;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use App\Services\WordService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class WordServiceTest extends TestCase
{
    protected MockInterface|WordRepositoryInterface $wordRepositoryMock;

    protected WordService $wordService;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->wordRepositoryMock = Mockery::mock(WordRepositoryInterface::class)->shouldIgnoreMissing();

        $this->wordService = new WordService($this->wordRepositoryMock);

        DB::shouldReceive('beginTransaction')->byDefault();
        DB::shouldReceive('commit')->byDefault();
        DB::shouldReceive('rollBack')->byDefault();
    }

    #[\Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_words_with_translations_returns_collection_from_repository(): void
    {
        $expectedCollection = new Collection([
            Mockery::mock(Word::class)->shouldIgnoreMissing(),
            Mockery::mock(Word::class)->shouldIgnoreMissing(),
        ]);

        $this->wordRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->wordService->getAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expectedCollection, $result);
        $this->assertCount(2, $result);
    }

    public function test_create_word_with_translations_success(): void
    {
        $wordData          = ['english_word' => 'test', 'translations' => ['es' => 'prueba']];
        $mockedCreatedWord = Mockery::mock(Word::class)->shouldIgnoreMissing();

        $this->wordRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($wordData)
            ->andReturn($mockedCreatedWord);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $result = $this->wordService->createWordWithTranslations($wordData);

        $this->assertTrue($result);
    }

    public function test_create_word_with_translations_repository_exception_rolls_back_and_throws_translation_exception(): void
    {
        $wordData            = ['english_word' => 'test', 'translations' => ['es' => 'prueba']];
        $repositoryException = new \Exception('Database error');

        $this->wordRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($wordData)
            ->andThrow($repositoryException);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->once();

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Error creating word and translations');

        try {
            $this->wordService->createWordWithTranslations($wordData);
        } catch (TranslationException $e) {
            $this->assertSame($repositoryException, $e->getPrevious());
            throw $e;
        }
    }

    public function test_show_word_with_translations_success(): void
    {
        $wordId         = 1;
        $mockedWord     = Mockery::mock(Word::class)->shouldIgnoreMissing();
        $mockedWord->id = $wordId;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn($mockedWord);

        $result = $this->wordService->showWordWithTranslations($wordId);

        $this->assertInstanceOf(Word::class, $result);
        $this->assertSame($mockedWord, $result);
    }

    public function test_show_word_with_translations_not_found_throws_exception(): void
    {
        $wordId = 999;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn(null);

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage("Word with id $wordId not found");

        $this->wordService->showWordWithTranslations($wordId);
    }

    public function test_update_word_with_translations_success(): void
    {
        $wordId          = 1;
        $newEnglishWord  = 'updated';
        $newTranslations = ['es' => 'actualizado'];

        $existingWordMock     = Mockery::mock(Word::class)->shouldIgnoreMissing();
        $existingWordMock->id = $wordId;

        $updatedWordMock               = Mockery::mock(Word::class)->shouldIgnoreMissing();
        $updatedWordMock->id           = $wordId;
        $updatedWordMock->english_word = $newEnglishWord;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn($existingWordMock);

        $this->wordRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->andReturn($updatedWordMock);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $result = $this->wordService->updateWordWithTranslations($wordId, $newEnglishWord, $newTranslations);

        $this->assertInstanceOf(Word::class, $result);
        $this->assertSame($existingWordMock, $result);
    }

    public function test_update_word_with_translations_not_found_returns_null(): void
    {
        $wordId          = 999;
        $newEnglishWord  = 'updated';
        $newTranslations = ['es' => 'actualizado'];

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn(null);

        $this->wordRepositoryMock->shouldNotReceive('update');

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->never();

        $result = $this->wordService->updateWordWithTranslations($wordId, $newEnglishWord, $newTranslations);
        $this->assertNull($result);
    }

    public function test_update_word_with_translations_repository_exception_rolls_back_and_throws_translation_exception(): void
    {
        $wordId              = 1;
        $newEnglishWord      = 'updated';
        $newTranslations     = ['es' => 'actualizado'];
        $repositoryException = new \Exception('Update failed');

        $existingWordMock     = Mockery::mock(Word::class)->shouldIgnoreMissing();
        $existingWordMock->id = $wordId;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn($existingWordMock);

        $this->wordRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::any(), $newEnglishWord, $newTranslations)
            ->andThrow($repositoryException);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->once();

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Error updating word and translations');

        try {
            $this->wordService->updateWordWithTranslations($wordId, $newEnglishWord, $newTranslations);
        } catch (TranslationException $e) {
            $this->assertSame($repositoryException, $e->getPrevious());
            throw $e;
        }
    }

    public function test_destroy_word_with_translations_success(): void
    {
        $wordId               = 1;
        $existingWordMock     = Mockery::mock(Word::class)->shouldIgnoreMissing();
        $existingWordMock->id = $wordId;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn($existingWordMock);

        $this->wordRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($existingWordMock)
            ->andReturn(true);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $result = $this->wordService->destroyWordWithTranslations($wordId);

        $this->assertTrue($result);
    }

    public function test_destroy_word_with_translations_not_found_throws_exception(): void
    {
        $wordId = 999;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn(null);

        $this->wordRepositoryMock->shouldNotReceive('delete');
        DB::shouldNotReceive('beginTransaction');
        DB::shouldNotReceive('commit');
        DB::shouldNotReceive('rollBack');

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage("Word with id $wordId not found");

        $this->wordService->destroyWordWithTranslations($wordId);
    }

    public function test_destroy_word_with_translations_repository_exception_rolls_back_and_throws_translation_exception(): void
    {
        $wordId              = 1;
        $repositoryException = new \Exception('Delete failed');

        $existingWordMock     = Mockery::mock(Word::class)->shouldIgnoreMissing();
        $existingWordMock->id = $wordId;

        $this->wordRepositoryMock
            ->shouldReceive('findWithTranslations')
            ->once()
            ->with($wordId)
            ->andReturn($existingWordMock);

        $this->wordRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::any())
            ->andThrow($repositoryException);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->once();

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Error deleting word and translations');

        try {
            $this->wordService->destroyWordWithTranslations($wordId);
        } catch (TranslationException $e) {
            $this->assertSame($repositoryException, $e->getPrevious());
            throw $e;
        }
    }
}
