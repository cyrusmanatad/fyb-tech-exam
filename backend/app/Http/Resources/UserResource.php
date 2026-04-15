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
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'roles'       => $this->getRoleNames(),              // ['admin', 'support']
            'permissions' => $this->getAllPermissions()
                                  ->pluck('name'),               // ['edit products', ...]
            'login' => $this->last_login_at?->diffForHumans() ?? "Long time ago.",
            // 'last_login_ip' => $this->last_login_ip,
            'status' => 'Active',
            'color' => 'green',
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
