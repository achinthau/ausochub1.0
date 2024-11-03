<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketItem extends Model
{
    use HasFactory;

    protected $fillable=[
        'ticket_id',
    	'item_id',
    	'portion_id',
    	'qty',
    	'unit_price',
    	'line_total',
    	'parent_item_id',
    ];


	public function item()
	{
		return $this->belongsTo(ItemMaster::class,'item_id');
	}
}
