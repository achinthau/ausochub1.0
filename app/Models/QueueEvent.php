<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueEvent extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "queue_events";

    protected $primaryKey = 'id';
}
