<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'year' => $this->year,
            'is_available' => ! $this->relationLoaded('activeLoan') || $this->activeLoan === null,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
        ];
    }
}