<?php

namespace App\Modules\Payment\Tests\Feature;

use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenewalWebhookTest extends TestCase
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

    public function test_a_subscription_can_be_renewed_successfully(): void
    {
        $this->user->update([
            'plan_id' => $this->plan->id,
            'plan_expires_at' => now()->addDays($this->plan->duration_days),
        ]);

        $this->travelTo(now()->addDays($this->plan->duration_days - 2));

        $this->post(route('v1.payments.renewal-webhook'), [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
        ])
        ->assertStatus(200);

        $this->user->refresh();
        $this->assertEquals($this->plan->id, $this->user->plan_id);
        $this->assertEquals(
            now()->addDays($this->plan->duration_days + 2)->toDateString(),
            $this->user->plan_expires_at->toDateString(),
        );
    }

    public function test_renewal_webhook_fails_when_user_has_not_already_subscribed_to_the_plan(): void
    {
        $this->post(route('v1.payments.renewal-webhook'), [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
        ])
        ->assertStatus(403);
    }

    public function test_renewal_webhook_fails_when_sending_invalid_fields(): void
    {
        $this->post(route('v1.payments.renewal-webhook'), [
            'user_id' => 320,
            'plan_id' => 135,
        ])
        ->assertStatus(422);

        $this->post(route('v1.payments.renewal-webhook'), [
            'user_id' => $this->user->id,
        ])
        ->assertStatus(422);
    }
}
