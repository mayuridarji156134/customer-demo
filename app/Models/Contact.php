<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'customer_id'
    ];

    // Contact belongs to a customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
