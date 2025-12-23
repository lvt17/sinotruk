<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPay extends Model
{
//    protected $fillable = ['user_id', 'customer_id', 'old_money', 'created_at', 'new_money', 'pay', 'canUpdate'];
    protected $fillable = ['user_id', 'customer_id', 'pay', 'discount_value', 'created_at', 'content'];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
	
}
