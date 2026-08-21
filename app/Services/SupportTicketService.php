<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Collection;

class SupportTicketService
{
    /**
     * List all support tickets with their customer, most recently submitted first.
     *
     * @return Collection<int, SupportTicket>
     */
    public function list(): Collection
    {
        return SupportTicket::query()
            ->with('customer:id,name')
            ->latest()
            ->get();
    }
}
