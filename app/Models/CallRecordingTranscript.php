<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRecordingTranscript extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";

    protected $fillable = [
        'uniqueid',
        'transcript',
        'summary',
        'reaction',
    ];  
}
