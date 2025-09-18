<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DialerCallStatusOption extends Model
{
    use HasFactory;

    protected $fillables = ['option','type'];
}
