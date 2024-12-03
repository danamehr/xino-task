<?php

namespace App\Modules\User\Http\V1\Resources;

use App\Modules\Subscription\Http\V1\Resources\PlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'plan_expires_at' => $this->plan_expires_at,
            'plan' => PlanResource::make($this->plan),
        ];
    }
}
