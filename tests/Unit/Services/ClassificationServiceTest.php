<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\ClassificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Tests\TestCase;

class ClassificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ClassificationService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClassificationService;
    }

    public function test_classify_returns_valid_slugs(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('networking, security'),
        ]);

        $result = $this->service->classify('firewall');

        $this->assertSame(['networking', 'security'], $result);
    }

    public function test_classify_returns_single_slug(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('databases'),
        ]);

        $result = $this->service->classify('SQL');

        $this->assertSame(['databases'], $result);
    }

    public function test_classify_filters_invalid_slugs_from_response(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('networking, invalid-category, web'),
        ]);

        $result = $this->service->classify('API');

        $this->assertSame(['networking', 'web'], $result);
    }

    public function test_classify_returns_other_when_no_valid_slugs(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('totally-wrong, gibberish'),
        ]);

        $result = $this->service->classify('unknownterm');

        $this->assertSame(['other'], $result);
    }

    public function test_classify_limits_to_three_categories(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('networking, web, cloud, devops'),
        ]);

        $result = $this->service->classify('Kubernetes');

        $this->assertCount(3, $result);
    }

    public function test_classify_returns_other_when_llm_returns_empty_text(): void
    {
        // No fixtures → PrismFake returns TextResponse with text: '' → parseSlugs → ['other']
        Prism::fake([]);

        $result = $this->service->classify('mutex');

        $this->assertSame(['other'], $result);
    }
}
