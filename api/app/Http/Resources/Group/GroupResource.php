<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'type'       => 'group',
            'id'         => $this->id,
            'attributes' => [
                'title'       => $this->title,
                'description' => $this->description,
                'users_count' => $this->users_count
            ]
        ];
    }
}
