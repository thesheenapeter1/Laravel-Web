<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * TECHNICAL COMPONENT: Laravel Eloquent Model
 * PURPOSE: This class represents the 'products' table in the database.
 * WHY IT IS USED: To interact with product data using PHP objects instead of raw SQL.
 * PROBLEM SOLVED: It provides an abstraction layer that ensures data integrity and 
 *      simplifies database operations like searching, filtering, and pagination.
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function category_relation()
    {
        return $this->belongsTo(Category::class, 'category', 'name');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
