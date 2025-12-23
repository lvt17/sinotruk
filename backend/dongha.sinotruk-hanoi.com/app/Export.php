<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = ['user_id', 'customer_id', 'total', 'created_at', 'cash', 'money', 'debt', 'isVAT', 'money_with_vat', 'discount', 'note', 'lock', 'diff_bulk', 'diff_retail'];
    public function products() {
        return $this->belongsToMany(Product::class)->orderBy('name')->withTrashed()->withPivot('id', 'count', 'price');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
