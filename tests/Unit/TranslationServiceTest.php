<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\TranslationException;
use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    private TranslationRepositoryInterface|MockInterface $repositoryMock;

    private TranslationService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(TranslationRepositoryInterface::class);
        $this->service        = new TranslationService($this->repositoryMock);
    }

    #[\Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_paginated_translations(): void
    {
        $perPage = 15;
        $page    = 1;

        $expectedPaginator = new LengthAwarePaginator([], 0, $perPage, $page);

        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->with($perPage, $page)
            ->andReturn($expectedPaginator);

        $result = $this->service->getAll($perPage, $page);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_returns_translation_when_found(): void
    {
        $translationId = 1;
        $expectedTranslation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn($expectedTranslation);

        $result = $this->service->get($translationId);

        $this->assertInstanceOf(Translation::class, $result);
        $this->assertSame($expectedTranslation, $result);
    }

    public function test_get_throws_exception_when_not_found(): void
    {
        $translationId = 999;

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn(null);

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage("Translation with id $translationId not found");

        $this->service->get($translationId);
    }

    public function test_create_translation_successfully(): void
    {
        $data = [
            'word_id' => 1,
            'language' => 'es',
            'translation' => 'prueba',
        ];
        $expectedTranslation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedTranslation);

        $result = $this->service->create($data);

        $this->assertSame($expectedTranslation, $result);
    }

    public function test_create_translation_throws_exception_on_failure(): void
    {
        $data = [
            'word_id' => 1,
            'language' => 'es',
            'translation' => 'prueba',
        ];

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Error creating translation');

        $this->service->create($data);
    }

    public function test_update_translation_successfully(): void
    {
        $translationId = 1;
        $existingTranslation = Mockery::mock(Translation::class);
        $existingTranslation->shouldReceive('getAttribute')->with('word_id')->andReturn(1);
        $existingTranslation->shouldReceive('getAttribute')->with('language')->andReturn('es');
        $existingTranslation->shouldReceive('getAttribute')->with('translation')->andReturn('old');
        $existingTranslation->shouldReceive('setAttribute')->andReturnSelf();

        $data = [
            'word_id' => 1,
            'language' => 'es',
            'translation' => 'new',
        ];

        $updatedTranslation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn($existingTranslation);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($existingTranslation, $data)
            ->andReturn($updatedTranslation);

        $result = $this->service->update($translationId, $data);

        $this->assertSame($updatedTranslation, $result);
    }

    public function test_update_translation_throws_exception_when_not_found(): void
    {
        $translationId = 999;
        $data = ['translation' => 'new'];

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn(null);

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage("Translation with id $translationId not found");

        $this->service->update($translationId, $data);
    }

    public function test_update_translation_throws_exception_on_repository_failure(): void
    {
        $translationId = 1;
        $existingTranslation = Mockery::mock(Translation::class);
        $existingTranslation->shouldReceive('getAttribute')->with('word_id')->andReturn(1);
        $existingTranslation->shouldReceive('getAttribute')->with('language')->andReturn('es');
        $existingTranslation->shouldReceive('getAttribute')->with('translation')->andReturn('old');
        $existingTranslation->shouldReceive('setAttribute')->andReturnSelf();

        $data = ['translation' => 'new'];

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn($existingTranslation);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Failed to update translation');

        $this->service->update($translationId, $data);
    }

    public function test_delete_translation_successfully(): void
    {
        $translationId = 1;
        $existingTranslation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn($existingTranslation);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($existingTranslation)
            ->andReturn(true);

        $this->service->delete($translationId);

        $this->assertTrue(true); // Assertion to confirm no exception was thrown
    }

    public function test_delete_translation_throws_exception_when_not_found(): void
    {
        $translationId = 999;

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn(null);

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage("Translation with id $translationId not found");

        $this->service->delete($translationId);
    }

    public function test_delete_translation_throws_exception_on_repository_failure(): void
    {
        $translationId = 1;
        $existingTranslation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($translationId)
            ->andReturn($existingTranslation);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Error deleting translation');

        $this->service->delete($translationId);
    }
}
