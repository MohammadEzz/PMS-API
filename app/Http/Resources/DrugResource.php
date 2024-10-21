<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DrugResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "brandname" => $this->brandname,
            "type" => $this->option->name,
            "type_id" => $this->option->id,
            "description" => $this->description,
            "barcode" => $this->barcode,
            "middleunitnum" => $this->middleunitnum,
            "smallunitnum" => $this->smallunitnum,
            "visible" => $this->visible,
            "created_by" => $this->createdBy->username,
            "created_by_id" => $this->createdBy->id,
            "created_at" => $this->created_at,
        ];
    }
}
