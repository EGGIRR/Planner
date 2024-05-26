<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "user_id"
    ];
    public function deskLists()
    {
        return $this->hasMany(DeskList::class);
    }
}
