<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['user_id', 'customer_id', 'created_at', 'discount', 'isVAT','note'];
    public function products() {
        return $this->belongsToMany(Product::class)->withTrashed()->withPivot('id', 'price', 'count')->orderBy('product_quote.id');;
    }

    public function customer() {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
