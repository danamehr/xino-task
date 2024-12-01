<?php

namespace App\Modules\Invoice\Http\V1\Resources;

use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Subscription\Http\V1\Resources\PlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'type' => InvoiceType::from($this->type)->name,
            'status' => InvoiceStatus::from($this->status)->name,
            'amount' => number_format($this->amount, 2),
            'duration_days' => $this->duration_days,
            'created_at' => $this->created_at,
            'verified_at' => $this->verified_at,
            'plan' => PlanResource::make($this->plan),
        ];
    }
}
