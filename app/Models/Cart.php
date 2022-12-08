<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = "cart";
    protected $fillable = [
        'id','user_id','product_id','quantity','product_mrp','total_mrp','created_at','updated_at'
    ];
}
