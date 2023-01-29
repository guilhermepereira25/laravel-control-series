<?php

namespace App\Http\Resources;

use App\Http\Requests\SeriesFormRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource
{
    public static $wrap = 'series';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function create(SeriesFormRequest $request)
    {
        dd($request->all());
    }
}
