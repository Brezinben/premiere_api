<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $linksActors=collect($this->whenLoaded('actors'))->map(function ($actor, $key) {
            return '/api/actors/' . $actor->id;
        });

        return [
            'links'  => '/api/movies/'.$this->id,
            'id' => $this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'release_date'=>$this->release_date,
            'director'=>$this->director,
            'producer'=>$this->producer,
            'category'=> new CategoryResource($this->whenLoaded('category')),
            "actors"=> $linksActors,

        ];
    }
}
