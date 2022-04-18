<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResultResource extends JsonResource
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
            'user_id' => $this->user_id,
            'domain' => $this->domain,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'description' => $this->description,
            'location' => $this->location,
            'logo' => $this->logo,
            'job_finished' => $this->job_finished,
        ];
    }
}
