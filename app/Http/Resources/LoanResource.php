<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'book' => new BookResource($this->whenLoaded('book')),
            'member' => [
                'id' => $this->member?->id,
                'name' => $this->member?->name,
                'email' => $this->member?->email,
            ],
            'borrowed_at' => $this->borrowed_at?->toDateString(),
            'due_at' => $this->due_at?->toDateString(),
            'returned_at' => $this->returned_at?->toDateString(),
            'is_late' => $this->isLate(),
        ];
    }
}