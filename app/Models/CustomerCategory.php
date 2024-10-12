<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    protected $fillable = ['name'];

    // A category has many customers
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
