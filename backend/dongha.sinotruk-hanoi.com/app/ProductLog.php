<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    protected $fillable = ['name', 'value', 'user_id', 'customer_id', 'status', 'product_id'];

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function customer() {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
