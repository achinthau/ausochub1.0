<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    public function subCategories()
    {
        return $this->hasMany(TicketSubCategory::class);
    }

    public function scopePrimary($query)
    {
        $query->whereIn('id',[1,2]);
    }
    public function scopeOrder($query)
    {
        $query->whereIn('id',[3]);
    }

}
