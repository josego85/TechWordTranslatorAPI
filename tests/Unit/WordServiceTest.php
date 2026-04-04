<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\WordNotFoundException;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use App\Services\ClassificationService;
use App\Services\WordService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class WordServiceTest extends TestCase
{
    protected MockInterface|WordRepositoryInterface $wordRepositoryMock;

    protected MockInterface|ClassificationService $classifierMock;

    protected MockInterface|CategoryRepositoryInterface $categoryRepositoryMock;

    protected WordService $wordService;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->wordRepositoryMock     = Mockery::mock(WordRepositoryInterface::class);
        $this->classifierMock         = Mockery::mock(ClassificationService::class);
        $this->categoryRepositoryMock = Mockery::mock(CategoryRepositoryInterface::class);

        $this->wordService = new WordService(
            $this->wordRepositoryMock,
            $this->classifierMock,
            $this->categoryRepositoryMock,
        );
    }

    #[\Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_paginated_words(): void
    {
        $perPage           = 15;
        $page              = 1;
        $expectedPaginator = new LengthAwarePaginator([], 0, $perPage, $page);

        $this->wordRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->with($perPage, $page, null, null, null)
            ->andReturn($expectedPaginator);

        $result = $this->wordService->getAll($perPage, $page);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_all_with_search_returns_filtered_results(): void
    {
        $perPage           = 10;
        $page              = 1;
        $search            = 'test';
        $expectedPaginator = new LengthAwarePaginator([], 0, $perPage, $page);

        $this->wordRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->with($perPage, $page, $search, null, null)
            ->andReturn($expectedPaginator);

        $result = $this->wordService->getAll($perPage, $page, $search);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_create_word_successfully(): void
    {
        $data         = ['english_word' => 'test'];
        $expectedWord = Mockery::mock(Word::class);

        $this->wordRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(['english_word' => 'test'])
            ->andReturn($expectedWord);

        // Classify returns [] — syncCategories returns early, no pivot interaction
        $this->classifierMock
            ->shouldReceive('classify')
            ->once()
            ->with('test')
            ->andReturn([]);

        $expectedWord->shouldReceive('getAttribute')->with('english_word')->andReturn('test');
        $expectedWord->shouldReceive('load')->with('categories')->andReturnSelf();

        $result = $this->wordService->create($data);

        $this->assertSame($expectedWord, $result);
    }

    public function test_create_word_throws_exception_on_failure(): void
    {
        $data = ['english_word' => 'test'];

        $this->wordRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage('Error creating word and translations');

        $this->wordService->create($data);
    }

    public function test_get_word_returns_word_when_found(): void
    {
        $wordId       = 1;
        $expectedWord = Mockery::mock(Word::class);

        $this->wordRepositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($wordId)
            ->andReturn($expectedWord);

        $result = $this->wordService->get($wordId);

        $this->assertSame($expectedWord, $result);
    }

    public function test_get_word_throws_exception_when_not_found(): void
    {
        $wordId = 999;

        $this->wordRepositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($wordId)
            ->andReturn(null);

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage("Word with id $wordId not found");

        $this->wordService->get($wordId);
    }

    public function test_update_word_successfully(): void
    {
        $wordId       = 1;
        $englishWord  = 'updated';
        $existingWord = Mockery::mock(Word::class);
        $updatedWord  = Mockery::mock(Word::class);

        $existingWord->shouldReceive('getAttribute')->with('english_word')->andReturn('original');

        $this->wordRepositoryMock->shouldReceive('get')->once()->with($wordId)->andReturn($existingWord);
        $this->wordRepositoryMock->shouldReceive('update')->once()->with($existingWord, $englishWord)->andReturn($updatedWord);

        // syncCategories calls $updatedWord->english_word then classify
        $updatedWord->shouldReceive('getAttribute')->with('english_word')->andReturn($englishWord);
        $this->classifierMock->shouldReceive('classify')->once()->with($englishWord)->andReturn([]);
        $updatedWord->shouldReceive('load')->with('categories')->andReturnSelf();

        $result = $this->wordService->update($wordId, $englishWord);

        $this->assertSame($updatedWord, $result);
    }

    public function test_update_word_throws_exception_when_not_found(): void
    {
        $this->wordRepositoryMock->shouldReceive('get')->once()->with(999)->andReturn(null);

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage('Word with id 999 not found');

        $this->wordService->update(999, 'updated');
    }

    public function test_update_word_throws_exception_on_repository_failure(): void
    {
        $wordId       = 1;
        $englishWord  = 'updated';
        $existingWord = Mockery::mock(Word::class);

        $existingWord->shouldReceive('getAttribute')->with('english_word')->andReturn('original');

        $this->wordRepositoryMock->shouldReceive('get')->once()->with($wordId)->andReturn($existingWord);
        $this->wordRepositoryMock->shouldReceive('update')->once()->andThrow(new \Exception('Database error'));

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage('Failed to update word');

        $this->wordService->update($wordId, $englishWord);
    }

    public function test_delete_word_successfully(): void
    {
        $wordId       = 1;
        $existingWord = Mockery::mock(Word::class);

        $this->wordRepositoryMock->shouldReceive('get')->once()->with($wordId)->andReturn($existingWord);
        $this->wordRepositoryMock->shouldReceive('delete')->once()->with($existingWord)->andReturn(true);

        $this->wordService->delete($wordId);

        $this->assertTrue(true);
    }

    public function test_delete_word_throws_exception_when_not_found(): void
    {
        $this->wordRepositoryMock->shouldReceive('get')->once()->with(999)->andReturn(null);

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage('Word with id 999 not found');

        $this->wordService->delete(999);
    }

    public function test_delete_word_throws_exception_on_repository_failure(): void
    {
        $wordId       = 1;
        $existingWord = Mockery::mock(Word::class);

        $this->wordRepositoryMock->shouldReceive('get')->once()->with($wordId)->andReturn($existingWord);
        $this->wordRepositoryMock->shouldReceive('delete')->once()->andThrow(new \Exception('Database error'));

        $this->expectException(WordNotFoundException::class);
        $this->expectExceptionMessage('Error deleting word');

        $this->wordService->delete($wordId);
    }
}
