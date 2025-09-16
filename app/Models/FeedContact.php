<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedContact extends Model
{
    use HasFactory;

    protected $table = 'feed_contacts';

    protected $fillable = ['feed_id', 'contact_no_01','contact_no_02','priority_field', 'data'];

    // protected $casts = [
    //     'data' => 'array', 
    // ];
}
