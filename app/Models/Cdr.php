<?php

namespace App\Models;

use App\Models\Scopes\CdrScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "cdr";


    public $timestamps = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new CdrScope);
    }

    public function getExtensionAttribute()
    {
        if (preg_match('/(?<=\/)[0-9]+(?=-)/', $this->dstchannel, $matches)) {
            return $matches[0];
        }
        return null;
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'extension', 'extension');
    }

     public function callCounts()
    {
        return $this->hasMany(CallCount::class, 'uniqueid', 'uniqueid');
    }

    public function queCouts()
    {
        return $this->hasMany(QueueCount::class, 'uniqueid', 'uniqueid');
    }

}
