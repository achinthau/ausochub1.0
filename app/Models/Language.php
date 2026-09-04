<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'user_languages';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'language_user', 'language_id', 'user_id');
    }
}
