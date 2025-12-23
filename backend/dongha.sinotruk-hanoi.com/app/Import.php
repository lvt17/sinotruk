<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = ['user_id',
        'customer_id',
        'total',
        'created_at',
        'cash',
        'money',
        'debt',
        'isVAT',
        'money_with_vat',
        'isProductError',
        'content',
        'discount',
        'note', 
        'lock',
        'diff_bulk', 
        'diff_retail'
    ];

    protected $casts = [
        'isProductError' => 'boolean',
    ];

    public function products() {
        return $this->belongsToMany(Product::class)->withTrashed()->withPivot('id', 'count', 'price');
    }

    public function customer() {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
