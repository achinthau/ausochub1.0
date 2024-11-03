<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MohClass extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_moh_class";
    public $timestamps = false;

}
