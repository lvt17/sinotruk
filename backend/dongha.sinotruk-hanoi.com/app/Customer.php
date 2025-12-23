<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    protected $fillable = ['name', 'money', 'phone', 'address', 'person', 'email', 'code', 'bulk_customer', 'user_id', 'monthly_discount'];
    use SoftDeletes; // use the trait
    protected $dates = ['deleted_at']; // mark this column as a date

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
	public function customerPays()
    {
        return $this->hasMany(CustomerPay::class);
    }
	
}
