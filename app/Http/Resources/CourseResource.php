<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $show = false;

    public function toArray($request)
    {
        $data = [
            "id"=> $this->id,
            "slug"=> $this->slug,
            "status"=> $this->status,
            "title"=> $this->title,
        ];
        return $data;
    }
}
