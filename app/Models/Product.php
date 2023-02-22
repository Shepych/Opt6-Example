<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];
    protected $hidden = ['id'];
    public $timestamps = false;

    public function orders() {
        return $this->belongsToMany(Order::class);
    }
}
