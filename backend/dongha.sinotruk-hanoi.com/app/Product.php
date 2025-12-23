<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'price', 'price_bulk', 'type', 'total', 'image', 'mansx', 'category_id', 'weight', 'order_pending', 'note' , 'min'];
    use SoftDeletes; // use the trait
    protected $dates = ['deleted_at']; // mark this column as a date

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_products');
    }

}
