<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MovieCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        //$links = $this->collection-cccccccccc;
        return [
            'count' => $this->collection->count(),
            'data' => $this->collection,
            //'links' => $links
        ];
    }
}
