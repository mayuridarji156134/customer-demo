<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'reference',
        'category_id',
        'start_date',
        'description'
    ];

    // Customer belongs to a category
    public function category()
    {
        return $this->belongsTo(CustomerCategory::class);
    }

    // Customer has many contacts
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
