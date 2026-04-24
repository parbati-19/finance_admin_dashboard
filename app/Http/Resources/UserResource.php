<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => (int) $this->status,
            'status_label' => $this->status === 1 ? 'Active' : 'Inactive',
            'role' => $this->whenLoaded('roles', fn() => $this->getRoleNames()->first()),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'deleted' => $this->trashed(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->when($this->trashed(), $this->deleted_at?->toISOString()),
            'detail' => UserDetailResource::make($this->whenLoaded('detail')),
        ];
    }
}
