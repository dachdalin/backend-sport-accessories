<?php

namespace App\Models;

use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    /** @use HasFactory<SupportTicketFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'subject',
        'type',
        'priority',
        'description',
        'attachment',
        'attachment_storage_type',
        'reply',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'priority' => 'low',
        'attachment_storage_type' => 'public',
        'status' => 'open',
    ];

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
