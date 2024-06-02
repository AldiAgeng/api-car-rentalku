<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Car extends Model
{
    use HasFactory, Searchable;


    protected $guarded = ['id'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url("images/" . $this->image);
        }
    }

    public function toSearchableArray()
    {
        return [
            'merk' => $this->merk,
            'model' => $this->model,
            'is_available' => $this->is_available
        ];
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
