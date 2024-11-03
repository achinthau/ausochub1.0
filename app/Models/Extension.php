<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Extension extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_exten";

    public $timestamps = false;

    protected $fillable = [
        'extension',
        'exten_type',
        // other attributes
    ];

    public function scopeNotAssigned($query,$agent = null)
    {
        $query->whereNotIn('extension',function($query){
            $query->select(DB::raw('DISTINCT extension'))
            ->from('au_user')
            ->whereNotNull('extension');
        });

        if ($agent) {
            $query->orWhereIn('extension',function($query) use ($agent){
                $query->select(DB::raw('DISTINCT extension'))
                ->from('au_user')
                ->where('id',$agent);
            });
        }
    }
}
