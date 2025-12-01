<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CObjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return parent::toArray($request);
    // }

    public function toArray(Request $request): array
    {
        if ($this->resource->owner) {
            $this->resource->owner->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
        }
        return [
            'filename' => $this->filename,
            'path' => $this->path,
            'remarks' => $this->remarks,
            'owner' => $this->owner,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
