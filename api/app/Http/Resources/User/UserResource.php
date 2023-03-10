<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type'       => 'user',
            'id'         => $this->id,
            'attributes' => [
                'login' => $this->login,
                'email' => $this->email,
                'role'  => $this->role->getLabel()
            ]
        ];
    }
}
