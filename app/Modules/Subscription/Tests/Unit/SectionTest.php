<?php

namespace App\Modules\Subscription\Tests\Unit;

use App\Modules\Subscription\Jobs\CacheSectionsJob;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Section;
use App\Modules\Subscription\Services\SectionServiceInterface;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $secondLevelPlan = Plan::query()->firstWhere('level', 2);
        $this->user = User::factory()
            ->create([
                'plan_id' => $secondLevelPlan->id,
                'plan_expires_at' => now()->addDays($secondLevelPlan->duration_days),
            ]);
    }

    public function test_section_service_can_get_section_by_slug_without_cache(): void
    {
        /** @var SectionServiceInterface $sectionService */
        $sectionService = app(SectionServiceInterface::class);

        Bus::fake();
        $section = Section::factory()->create();

        $this->assertDatabaseHas(Section::class, ['slug' => $section->slug]);
        $this->assertFalse(Cache::has("subscriptions.sections.{$section->slug}"));

        $retrievedSection = $sectionService->getSection($section->slug);

        $this->assertInstanceOf(Section::class, $retrievedSection);
        $this->assertEquals($section->id, $retrievedSection->id);
        Bus::assertDispatched(CacheSectionsJob::class);
    }

    public function test_section_service_can_get_section_by_slug_using_cache(): void
    {
        /** @var SectionServiceInterface $sectionService */
        $sectionService = app(SectionServiceInterface::class);

        $section = Section::factory()->create();
        $this->assertDatabaseHas(Section::class, ['slug' => $section->slug]);

        $cacheSectionsJob = new CacheSectionsJob();
        $cacheSectionsJob->handle();
        $this->assertTrue(Cache::has("subscriptions.sections.{$section->slug}"));

        Bus::fake();
        $retrievedSection = $sectionService->getSection($section->slug);

        $this->assertInstanceOf(Section::class, $retrievedSection);
        $this->assertEquals($section->id, $retrievedSection->id);
        Bus::assertNotDispatched(CacheSectionsJob::class);
    }

    public function test_section_service_can_get_sections_without_cache(): void
    {
        /** @var SectionServiceInterface $sectionService */
        $sectionService = app(SectionServiceInterface::class);

        Bus::fake();

        $this->assertDatabaseCount(Section::class, 9);
        $this->assertFalse(Cache::has('subscriptions.section-levels.1'));
        $this->assertFalse(Cache::has('subscriptions.section-levels.2'));
        $this->assertFalse(Cache::has('subscriptions.section-levels.3'));

        $retrievedSections = $sectionService->getSections($this->user);

        $this->assertCount(6, $retrievedSections);
        Bus::assertDispatched(CacheSectionsJob::class);
    }

    public function test_section_service_can_get_sections_using_cache(): void
    {
        /** @var SectionServiceInterface $sectionService */
        $sectionService = app(SectionServiceInterface::class);

        $this->assertDatabaseCount(Section::class, 9);
        $this->assertFalse(Cache::has('subscriptions.section-levels.1'));
        $this->assertFalse(Cache::has('subscriptions.section-levels.2'));
        $this->assertFalse(Cache::has('subscriptions.section-levels.3'));

        $cacheSectionsJob = new CacheSectionsJob();
        $cacheSectionsJob->handle();
        $this->assertTrue(Cache::has('subscriptions.section-levels.1'));
        $this->assertTrue(Cache::has('subscriptions.section-levels.2'));
        $this->assertTrue(Cache::has('subscriptions.section-levels.3'));

        Bus::fake();
        $retrievedSections = $sectionService->getSections($this->user);

        $this->assertCount(6, $retrievedSections);
        Bus::assertNotDispatched(CacheSectionsJob::class);
    }

    public function test_section_service_throws_not_found_exception_when_section_slug_is_wrong(): void
    {
        /** @var SectionServiceInterface $sectionService */
        $sectionService = app(SectionServiceInterface::class);

        $section = Section::factory()->create();

        $this->assertDatabaseHas(Section::class, ['slug' => $section->slug]);

        $this->assertThrows(
            fn () => $sectionService->getSection($section->slug.'-test'),
            ModelNotFoundException::class
        );
    }
}
