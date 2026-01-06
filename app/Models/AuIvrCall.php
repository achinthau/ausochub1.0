<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuIvrCall extends Model
{
    use HasFactory;

    protected $connection = 'mysql-old';
    protected $table = 'au_ivr_calls';
    public $timestamps = false;

    public function cdr()
    {
        return $this->belongsTo(Cdr::class, 'uniqueid', 'uniqueid');
    }
}
