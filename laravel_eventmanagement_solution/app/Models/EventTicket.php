<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;

    protected $table = 'eventtickets';


    public function ticketCategories()
    {
        return $this->belongsTo(TicketCategory::class,  'ticket_category_id');
    }
}
