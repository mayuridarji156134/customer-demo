<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;

class CategoryController extends Controller
{
    public function index()
    {
        return CustomerCategory::all(); // Returns all categories as JSON
    }
}
