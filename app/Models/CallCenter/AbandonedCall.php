<?php

namespace App\Models\CallCenter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCall extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    public $timestamps = false;

    protected $primaryKey = 'uniqueid';
}
