<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'email_verified' => !is_null($this->email_verified_at),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'two_factor_enabled' => !is_null($this->two_factor_confirmed_at),
            'role' => $this->getRoleNames()->first(),
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
