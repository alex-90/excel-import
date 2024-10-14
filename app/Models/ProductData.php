<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductData extends Model
{
    protected $table = 'tblProductData';
    public $timestamps = false;
    protected $guarded = [];

    use HasFactory;
}
