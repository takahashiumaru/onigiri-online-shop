<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'is_available',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'is_available' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalSoldAttribute(): int
    {
        return (int) $this->orderItems()->sum('quantity');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            return \Storage::url($this->image);
        }
        return asset('images/default-onigiri.jpg');
    }

    public function isInStock(): bool
    {
        return $this->stock > 0 && $this->is_available;
    }
}
