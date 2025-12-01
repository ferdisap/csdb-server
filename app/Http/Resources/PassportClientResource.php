<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassportClientResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'owner' => $this->owner,
            'is_public' => $this->secret ? false : true,
            'provider' => $this->provider,
            'redirect_uris' => $this->redirect_uris,
            'grant_type' => $this->grant_type,
            'revoked' => (bool) $this->revoked,
            'update_at' => $this->updated_at,
        ];
    }
}
