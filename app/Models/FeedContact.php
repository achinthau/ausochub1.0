<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedContact extends Model
{
    use HasFactory;

    protected $table = 'feed_contacts';

    protected $fillable = ['feed_id', 'phone', 'customer_name', 'data'];

    // protected $casts = [
    //     'data' => 'array', 
    // ];
}
