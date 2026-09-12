<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'registered_at' => $this->registered_at?->toDateString(),
            'active_loans_count' => $this->when(isset($this->active_loans_count), $this->active_loans_count),
        ];
    }
}