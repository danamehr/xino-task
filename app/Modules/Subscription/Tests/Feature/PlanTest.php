<?php

namespace App\Modules\Subscription\Tests\Feature;

use App\Modules\Invoice\Models\Invoice;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    protected Plan $plan;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->plan = Plan::query()->first();
        $this->user = User::query()->first();
    }

    public function test_user_can_subscribe_to_a_plan(): void
    {
        $this->actingAs($this->user)
            ->post(route('v1.subscriptions.plans.subscribe', $this->plan->id))
            ->assertStatus(200);

        $this->user->refresh();
        $this->assertEquals($this->plan->id, $this->user->plan_id);
        $this->assertNotNull($this->user->plan_expires_at);
        $this->assertEquals(now()->addDays($this->plan->duration_days)->toDateString(), $this->user->plan_expires_at->toDateString());
        $this->assertDatabaseCount(Invoice::class, 1);
    }

    public function test_user_can_not_subscribe_to_a_plan_when_already_has_an_active_plan(): void
    {
        $this->user->update([
            'plan_id' => $this->plan->id,
            'plan_expires_at' => now()->addDays($this->plan->duration_days),
        ]);

        $this->actingAs($this->user)
            ->post(route('v1.subscriptions.plans.subscribe', $this->plan->id))
            ->assertStatus(403);
    }
}
