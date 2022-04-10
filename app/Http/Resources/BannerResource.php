<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'data' => json_decode($this->data),
            'preview' => asset(Storage::url($this->preview)),
            'is_premium' => $this->is_premium,
            'conversion' => $this->conversion,
            'used' => $this->used,
            'updated_at' => $this->updated_at,
        ];
    }
}



