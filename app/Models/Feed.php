<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'uploaded_by','file_name','status'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(FeedContactValid::class, 'feed_id');
    }

}
