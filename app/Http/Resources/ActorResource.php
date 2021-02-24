<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ActorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $linksMovies=collect($this->whenLoaded('movies'))->map(function ($movie, $key) {
            return '/api/movies/' . $movie->id;
        });

        return [
            'link'=>'/api/actors/'.$this->id,
            'id' => $this->id,
            'full_name'=>$this->full_name,
            'movies' =>  $linksMovies
        ];
    }
}
