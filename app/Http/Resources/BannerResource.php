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
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'data' => json_decode($this->data, true),
            'preview' => asset(Storage::url($this->preview)),
            'files' => FileResource::collection($this->files),
            'is_premium' => $this->is_premium,
            'conversion' => $this->conversion,
            'used' => $this->used,
            'view_count' => $this->view_count,
            'created_at' => $this->created_at,
        ];
    }
}



