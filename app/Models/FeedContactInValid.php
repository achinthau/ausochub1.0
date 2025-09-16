<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedContactInValid extends Model
{
    use HasFactory;

    protected $table = 'feed_contact_in_valids';

    protected $fillable = ['feed_id',  'contact_no_01','contact_no_02', 'priority_field', 'data'];

    // protected $casts = [
    //     'data' => 'array', 
    // ];
}
