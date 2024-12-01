<?php

namespace App\Modules\Subscription\Tests\Feature;

use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Section;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    protected Section $firstLevelSection;

    protected Section $secondLevelSection;

    protected Section $thirdLevelSection;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->firstLevelSection = Section::query()->firstWhere('required_level', 1);
        $this->secondLevelSection = Section::query()->firstWhere('required_level', 2);
        $this->thirdLevelSection = Section::query()->firstWhere('required_level', 3);
        $secondLevelPlan = Plan::query()->firstWhere('level', 2);
        $this->user = User::factory()
            ->create([
                'plan_id' => $secondLevelPlan->id,
                'plan_expires_at' => now()->addDays($secondLevelPlan->duration_days),
            ]);
    }

    public function test_user_can_access_to_the_sections_of_subscribed_plan(): void
    {
        $this->actingAs($this->user)
            ->get(route('v1.subscriptions.sections.show', $this->firstLevelSection->slug))
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->get(route('v1.subscriptions.sections.show', $this->secondLevelSection->slug))
            ->assertStatus(200);
    }

    public function test_user_can_not_access_to_the_sections_of_higher_level_plans(): void
    {
        $this->actingAs($this->user)
            ->get(route('v1.subscriptions.sections.show', $this->thirdLevelSection->slug))
            ->assertStatus(403);
    }

    public function test_user_can_not_access_to_the_sections_without_an_active_subscription_plan(): void
    {
        $this->actingAs(User::query()->first())
            ->get(route('v1.subscriptions.sections.show', $this->firstLevelSection->slug))
            ->assertStatus(403);
    }

    public function test_user_can_not_access_to_the_sections_with_an_expired_subscription_plan(): void
    {
        $this->user->update(['plan_expires_at' => now()->subDays(10)]);

        $this->actingAs($this->user)
            ->get(route('v1.subscriptions.sections.show', $this->firstLevelSection->slug))
            ->assertStatus(403);
    }

    public function test_user_can_only_see_the_section_list_of_subscribed_plan(): void
    {
        $this->actingAs($this->user)
            ->get(route('v1.subscriptions.sections.index'))
            ->assertStatus(200)
            ->assertJsonCount(6, 'data')
            ->assertJson([
                'data' => [
                    [
                        'required_level' => 1,
                    ],
                    [
                        'required_level' => 1,
                    ],
                    [
                        'required_level' => 1,
                    ],
                    [
                        'required_level' => 2,
                    ],
                    [
                        'required_level' => 2,
                    ],
                    [
                        'required_level' => 2,
                    ],
                ],
            ]);
    }

    public function test_user_can_not_see_the_section_list_without_a_subscription_plan(): void
    {
        $this->actingAs(User::query()->first())
            ->get(route('v1.subscriptions.sections.index'))
            ->assertStatus(403);
    }
}
