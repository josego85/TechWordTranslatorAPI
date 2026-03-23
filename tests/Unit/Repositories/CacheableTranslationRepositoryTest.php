<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use App\Repositories\CacheableTranslationRepository;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CacheableTranslationRepositoryTest extends TestCase
{
    private TranslationRepositoryInterface|MockInterface $repositoryMock;

    private CacheService|MockInterface $cacheMock;

    private CacheableTranslationRepository $decorator;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(TranslationRepositoryInterface::class);
        $this->cacheMock      = Mockery::mock(CacheService::class);
        $this->decorator      = new CacheableTranslationRepository(
            $this->repositoryMock,
            $this->cacheMock
        );
    }

    #[\Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_uses_cache_remember(): void
    {
        $perPage    = 15;
        $page       = 1;
        $cacheKey   = 'translations:perPage:15:page:1';
        $paginator  = new LengthAwarePaginator([], 0, $perPage, $page);

        $this->cacheMock
            ->shouldReceive('generateTranslationsKey')
            ->once()
            ->with($perPage, $page)
            ->andReturn($cacheKey);

        $this->cacheMock
            ->shouldReceive('remember')
            ->once()
            ->with($cacheKey, Mockery::type('callable'))
            ->andReturnUsing(fn ($key, $callback) => $callback());

        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->with($perPage, $page)
            ->andReturn($paginator);

        $result = $this->decorator->getAll($perPage, $page);

        $this->assertSame($paginator, $result);
    }

    public function test_get_uses_cache_remember(): void
    {
        $id          = 5;
        $cacheKey    = 'translation:5';
        $translation = Mockery::mock(Translation::class);

        $this->cacheMock
            ->shouldReceive('generateTranslationKey')
            ->once()
            ->with($id)
            ->andReturn($cacheKey);

        $this->cacheMock
            ->shouldReceive('remember')
            ->once()
            ->with($cacheKey, Mockery::type('callable'))
            ->andReturnUsing(fn ($key, $callback) => $callback());

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($id)
            ->andReturn($translation);

        $result = $this->decorator->get($id);

        $this->assertSame($translation, $result);
    }

    public function test_get_returns_null_when_not_found(): void
    {
        $id       = 999;
        $cacheKey = 'translation:999';

        $this->cacheMock
            ->shouldReceive('generateTranslationKey')
            ->once()
            ->with($id)
            ->andReturn($cacheKey);

        $this->cacheMock
            ->shouldReceive('remember')
            ->once()
            ->with($cacheKey, Mockery::type('callable'))
            ->andReturnUsing(fn ($key, $callback) => $callback());

        $this->repositoryMock
            ->shouldReceive('get')
            ->once()
            ->with($id)
            ->andReturn(null);

        $result = $this->decorator->get($id);

        $this->assertNull($result);
    }

    public function test_create_invalidates_list_cache(): void
    {
        $data        = ['word_id' => 1, 'language' => 'es', 'translation' => 'prueba'];
        $translation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($translation);

        $this->cacheMock
            ->shouldReceive('forget')
            ->once()
            ->with('translations:*');

        $result = $this->decorator->create($data);

        $this->assertSame($translation, $result);
    }

    public function test_update_invalidates_specific_and_list_cache(): void
    {
        $id          = 3;
        $cacheKey    = 'translation:3';
        $data        = ['translation' => 'updated'];
        $translation = Mockery::mock(Translation::class);
        $translation->shouldReceive('getAttribute')->with('id')->andReturn($id);

        $updated = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($translation, $data)
            ->andReturn($updated);

        $this->cacheMock
            ->shouldReceive('generateTranslationKey')
            ->once()
            ->with($id)
            ->andReturn($cacheKey);

        $this->cacheMock
            ->shouldReceive('forget')
            ->once()
            ->with([$cacheKey, 'translations:*']);

        $result = $this->decorator->update($translation, $data);

        $this->assertSame($updated, $result);
    }

    public function test_update_does_not_invalidate_cache_when_update_fails(): void
    {
        $data        = ['translation' => 'updated'];
        $translation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($translation, $data)
            ->andReturn(null);

        $this->cacheMock->shouldNotReceive('forget');

        $result = $this->decorator->update($translation, $data);

        $this->assertNull($result);
    }

    public function test_delete_invalidates_specific_and_list_cache(): void
    {
        $id          = 7;
        $cacheKey    = 'translation:7';
        $translation = Mockery::mock(Translation::class);
        $translation->shouldReceive('getAttribute')->with('id')->andReturn($id);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($translation)
            ->andReturn(true);

        $this->cacheMock
            ->shouldReceive('generateTranslationKey')
            ->once()
            ->with($id)
            ->andReturn($cacheKey);

        $this->cacheMock
            ->shouldReceive('forget')
            ->once()
            ->with([$cacheKey, 'translations:*']);

        $result = $this->decorator->delete($translation);

        $this->assertTrue($result);
    }

    public function test_delete_does_not_invalidate_cache_when_delete_fails(): void
    {
        $translation = Mockery::mock(Translation::class);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($translation)
            ->andReturn(false);

        $this->cacheMock->shouldNotReceive('forget');

        $result = $this->decorator->delete($translation);

        $this->assertFalse($result);
    }
}
