<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type?->label(),
            'company_name' => $this->company_name,
            'company_pan' => $this->company_pan,
            'contact_person' => $this->contact_person,
            'company_address' => $this->company_address,
            'company_phone' => $this->company_phone,
            'personal_phone' => $this->personal_phone,
            'company_email' => $this->company_email,
            'is_verified' => $this->is_verified,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
