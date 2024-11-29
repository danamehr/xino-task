<?php

namespace App\Modules\Invoice\Http\V1\Controllers;

use App\Modules\Invoice\Http\V1\Resources\InvoiceResource;
use App\Modules\Invoice\Services\InvoiceServiceInterface;
use App\Modules\Shared\Http\V1\Controller;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceServiceInterface $invoiceService)
    {
    }

    public function index()
    {
        return InvoiceResource::collection($this->invoiceService->getInvoices(auth()->user()));
    }
}
