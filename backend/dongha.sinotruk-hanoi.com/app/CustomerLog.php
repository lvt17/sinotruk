<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerLog extends Model
{
    protected $fillable = ['name', 'value', 'customer_id', 'user_id'];
    public function customer() {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
