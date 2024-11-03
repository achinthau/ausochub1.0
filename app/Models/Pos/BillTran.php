<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillTran extends Model
{
    use HasFactory;

    protected $connection = "mr-kottu-sqlsrv";
    protected $table = "bill_tran";
    
    // protected $primaryKey = 'bill_no';
    public $timestamps = false;

    public $incrementing = false;
}
