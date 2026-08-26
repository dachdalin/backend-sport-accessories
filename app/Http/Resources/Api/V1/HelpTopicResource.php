<?php

namespace App\Http\Resources\Api\V1;

use App\Models\HelpTopic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HelpTopic */
class HelpTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'question' => $this->question,
            'answer' => $this->answer,
            'ranking' => $this->ranking,
        ];
    }
}
