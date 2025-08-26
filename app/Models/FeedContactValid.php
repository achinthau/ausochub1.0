<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedContactValid extends Model
{
    use HasFactory;

    protected $table = 'feed_contact_valids';

    protected $fillable = ['feed_id', 'phone', 'customer_name', 'data'];

    // protected $casts = [
    //     'data' => 'array', 
    // ];
}
