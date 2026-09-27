<?php

namespace App\Http\Resources;

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
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'is_active' => (bool) $this->is_active,
            'last_login_at' => $this->last_login_at?->toDateTimeString(),
            'last_login_ip' => $this->last_login_ip,
            'login' => $this->last_login_at?->diffForHumans() ?? 'Long time ago.',
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'color' => $this->is_active ? 'green' : 'red',
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
