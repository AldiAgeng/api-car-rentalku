<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $guarded = ['id'];


    public function car()
    {
        return $this->belongsTo(Car::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
