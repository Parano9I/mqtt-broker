<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class SensorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type'       => 'sensor',
            'id'         => $this->uuid,
            'attributes' => [
                'name'       => $this->name,
                'description' => $this->description,
                'status' => $this->status,
                'path' => $this->path,
                'secret' => $this->secret,
                'topic' => $this->topic_id
            ]
        ];
    }
}
