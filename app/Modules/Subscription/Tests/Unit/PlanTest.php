<?php

namespace App\Modules\Subscription\Tests\Unit;

use App\Modules\Invoice\DTOs\InvoiceDTO;
use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Invoice\Services\InvoiceServiceInterface;
use App\Modules\Payment\DTOs\PaymentVerificationDTO;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Exceptions\InvalidPaymentException;
use App\Modules\Payment\Exceptions\UnsuccessfulPaymentException;
use App\Modules\Payment\Services\PaymentServiceInterface;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Services\PlanServiceInterface;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
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

    public function test_plan_service_can_subscribe_user_to_a_plan_with_successful_payment(): void
    {
        $this->assertNull($this->user->plan_id);
        $this->assertNull($this->user->plan_expires_at);

        $mockInvoiceService = Mockery::mock(InvoiceServiceInterface::class);
        $mockInvoiceService
            ->shouldReceive('makeInvoice')
            ->once()
            ->with($this->user, $this->plan, InvoiceType::Initial)
            ->andReturn($invoiceDto = new InvoiceDTO(
                $this->user->id,
                $this->plan->id,
                InvoiceType::Initial,
                InvoiceStatus::UnPaid,
                $this->plan->price,
                $this->plan->duration_days,
            ));

        $mockInvoiceService
            ->shouldReceive('createInvoice')
            ->once()
            ->with($invoiceDto)
            ->andReturn(new Invoice());

        $mockPaymentService = Mockery::mock(PaymentServiceInterface::class);
        $mockPaymentService
            ->shouldReceive('verifyPayment')
            ->once()
            ->with($invoiceDto)
            ->andReturn(new PaymentVerificationDTO(
                PaymentStatus::Successful,
                str()->uuid(),
            ));

        app()->instance(PaymentServiceInterface::class, $mockPaymentService);
        app()->instance(InvoiceServiceInterface::class, $mockInvoiceService);

        /** @var PlanServiceInterface $planService */
        $planService = app(PlanServiceInterface::class);

        $updatedUser = $planService->subscribe($this->user, $this->plan);

        $this->assertEquals($this->plan->id, $updatedUser->plan_id);
        $this->assertNotNull($updatedUser->plan_expires_at);
        $this->assertEquals(now()->addDays($this->plan->duration_days)->toDateString(), $updatedUser->plan_expires_at->toDateString());
    }

    public function test_plan_service_throws_exception_on_an_unsuccessful_payment_when_subscribing_user(): void
    {
        $this->assertNull($this->user->plan_id);
        $this->assertNull($this->user->plan_expires_at);

        $mockInvoiceService = Mockery::mock(InvoiceServiceInterface::class);
        $mockInvoiceService
            ->shouldReceive('makeInvoice')
            ->once()
            ->with($this->user, $this->plan, InvoiceType::Initial)
            ->andReturn($invoiceDto = new InvoiceDTO(
                $this->user->id,
                $this->plan->id,
                InvoiceType::Initial,
                InvoiceStatus::UnPaid,
                $this->plan->price,
                $this->plan->duration_days,
            ));

        $mockInvoiceService
            ->shouldNotReceive('createInvoice');

        $mockPaymentService = Mockery::mock(PaymentServiceInterface::class);
        $mockPaymentService
            ->shouldReceive('verifyPayment')
            ->once()
            ->with($invoiceDto)
            ->andReturn(new PaymentVerificationDTO(
                PaymentStatus::Unsuccessful,
            ));

        app()->instance(PaymentServiceInterface::class, $mockPaymentService);
        app()->instance(InvoiceServiceInterface::class, $mockInvoiceService);

        /** @var PlanServiceInterface $planService */
        $planService = app(PlanServiceInterface::class);

        $this->assertThrows(
            fn() => $planService->subscribe($this->user, $this->plan),
            UnsuccessfulPaymentException::class,
        );

        $this->user->refresh();

        $this->assertNull($this->user->plan_id);
        $this->assertNull($this->user->plan_expires_at);
    }

    public function test_plan_service_can_renew_user_subscription(): void
    {
        $this->user->update([
            'plan_id' => $this->plan->id,
            'plan_expires_at' => now()->addDays($this->plan->duration_days),
        ]);

        $this->travelTo(now()->addDays($this->plan->duration_days - 2));

        $mockInvoiceService = Mockery::mock(InvoiceServiceInterface::class);
        $mockInvoiceService
            ->shouldReceive('makeInvoice')
            ->once()
            ->with($this->user, $this->plan, InvoiceType::Renewal)
            ->andReturn($invoiceDto = new InvoiceDTO(
                $this->user->id,
                $this->plan->id,
                InvoiceType::Renewal,
                InvoiceStatus::UnPaid,
                $this->plan->price,
                $this->plan->duration_days,
            ));

        $mockInvoiceService
            ->shouldReceive('createInvoice')
            ->once()
            ->with($invoiceDto)
            ->andReturn(new Invoice());

        $mockPaymentService = Mockery::mock(PaymentServiceInterface::class);
        $mockPaymentService
            ->shouldReceive('verifyPayment')
            ->once()
            ->with($invoiceDto)
            ->andReturn(new PaymentVerificationDTO(
                PaymentStatus::Successful,
                str()->uuid(),
            ));

        app()->instance(PaymentServiceInterface::class, $mockPaymentService);
        app()->instance(InvoiceServiceInterface::class, $mockInvoiceService);

        /** @var PlanServiceInterface $planService */
        $planService = app(PlanServiceInterface::class);

        $this->assertTrue($planService->renew($this->user, $this->plan));

        $this->user->refresh();
        $this->assertEquals($this->plan->id, $this->user->plan_id);
        $this->assertEquals(
            now()->addDays($this->plan->duration_days + 2)->toDateString(),
            $this->user->plan_expires_at->toDateString(),
        );
    }

    public function test_plan_service_throws_exception_on_an_invalid_payment_when_trying_to_renew_user_subscription(): void
    {
        $this->user->update([
            'plan_id' => $this->plan->id,
            'plan_expires_at' => now()->addDays($this->plan->duration_days),
        ]);

        $this->travelTo(now()->addDays($this->plan->duration_days - 2));

        $mockInvoiceService = Mockery::mock(InvoiceServiceInterface::class);
        $mockInvoiceService
            ->shouldReceive('makeInvoice')
            ->once()
            ->with($this->user, $this->plan, InvoiceType::Renewal)
            ->andReturn($invoiceDto = new InvoiceDTO(
                $this->user->id,
                $this->plan->id,
                InvoiceType::Renewal,
                InvoiceStatus::UnPaid,
                $this->plan->price,
                $this->plan->duration_days,
            ));

        $mockInvoiceService
            ->shouldNotReceive('createInvoice');

        $mockPaymentService = Mockery::mock(PaymentServiceInterface::class);
        $mockPaymentService
            ->shouldReceive('verifyPayment')
            ->once()
            ->with($invoiceDto)
            ->andReturn(new PaymentVerificationDTO(
                PaymentStatus::Unsuccessful,
            ));

        app()->instance(PaymentServiceInterface::class, $mockPaymentService);
        app()->instance(InvoiceServiceInterface::class, $mockInvoiceService);

        /** @var PlanServiceInterface $planService */
        $planService = app(PlanServiceInterface::class);

        $this->assertThrows(
            fn() => $planService->renew($this->user, $this->plan),
            InvalidPaymentException::class,
        );
    }

    public function test_plan_service_can_get_all_plans(): void
    {
        /** @var PlanServiceInterface $planService */
        $planService = app(PlanServiceInterface::class);

        $this->assertDatabaseCount(Plan::class, 3);

        $retrievedPlans = $planService->getPlans();

        $this->assertCount(3, $retrievedPlans);
    }
}
