<?php

namespace App\Modules\Invoice\Tests\Unit;

use App\Modules\Invoice\DTOs\InvoiceDTO;
use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Invoice\Services\InvoiceServiceInterface;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
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

    public function test_invoice_service_can_make_initial_subscription_invoice(): void
    {
        /** @var InvoiceServiceInterface $invoiceService */
        $invoiceService = app(InvoiceServiceInterface::class);

        $invoiceDto = $invoiceService->makeInvoice($this->user, $this->plan, InvoiceType::Initial);

        $this->assertEquals($this->user->id, $invoiceDto->userId);
        $this->assertEquals($this->plan->id, $invoiceDto->planId);
        $this->assertEquals($this->plan->duration_days, $invoiceDto->durationDays);
        $this->assertEquals($this->plan->price, $invoiceDto->amount);
        $this->assertEquals(InvoiceStatus::UnPaid, $invoiceDto->status);
        $this->assertEquals(InvoiceType::Initial, $invoiceDto->type);
    }

    public function test_invoice_service_can_save_subscription_invoice(): void
    {
        /** @var InvoiceServiceInterface $invoiceService */
        $invoiceService = app(InvoiceServiceInterface::class);

        $invoiceDto = new InvoiceDTO(
            $this->user->id,
            $this->plan->id,
            InvoiceType::Initial,
            InvoiceStatus::Paid,
            $this->plan->price,
            $this->plan->duration_days,
            str()->uuid(),
            now(),
        );

        $this->assertDatabaseEmpty(Invoice::class);
        $invoice = $invoiceService->createInvoice($invoiceDto);

        $this->assertDatabaseCount(Invoice::class, 1);
        $this->assertEquals($invoiceDto->userId, $invoice->user_id);
        $this->assertEquals($invoiceDto->planId, $invoice->plan_id);
        $this->assertEquals($invoiceDto->durationDays, $invoice->duration_days);
        $this->assertEquals($invoiceDto->amount, $invoice->amount);
        $this->assertEquals($invoiceDto->status->value, $invoice->status);
        $this->assertEquals($invoiceDto->type->value, $invoice->type);
    }

    public function test_invoice_service_can_get_user_invoices(): void
    {
        /** @var InvoiceServiceInterface $invoiceService */
        $invoiceService = app(InvoiceServiceInterface::class);

        Invoice::factory()
            ->count(3)
            ->for($this->user)
            ->for($this->plan)
            ->create();

        Invoice::factory()
            ->count(7)
            ->for($this->plan)
            ->create();

        $this->assertDatabaseCount(Invoice::class, 10);

        $retrievedInvoices = $invoiceService->getInvoices($this->user);

        $this->assertCount(3, $retrievedInvoices);
    }
}
