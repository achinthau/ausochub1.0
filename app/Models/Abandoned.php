<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abandoned extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_abandoned";

    public $timestamps = false;
}
