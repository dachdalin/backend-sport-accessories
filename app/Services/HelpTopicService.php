<?php

namespace App\Services;

use App\Models\HelpTopic;
use Illuminate\Database\Eloquent\Collection;

class HelpTopicService
{
    /**
     * List all help topics, lowest ranking (highest priority) first.
     *
     * @return Collection<int, HelpTopic>
     */
    public function list(): Collection
    {
        return HelpTopic::query()
            ->orderBy('ranking')
            ->latest()
            ->get();
    }
}
