<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'price',
        'quantity',
        'description',
        'image_path'
    ];

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    
    public function getStockStatusAttribute()
    {
        static $threshold;
        if ($threshold === null) {
            $threshold = (int) (\App\Models\Setting::where('key', 'low_stock_threshold')->value('value') ?? 10);
        }

        if ($this->quantity <= 0) return 'Out of Stock';
        if ($this->quantity < $threshold) return 'Low Stock';
        return 'In Stock';
    }

    public function getStatusColorAttribute()
    {
        static $threshold;
        if ($threshold === null) {
            $threshold = (int) (\App\Models\Setting::where('key', 'low_stock_threshold')->value('value') ?? 10);
        }

        if ($this->quantity <= 0) return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        if ($this->quantity < $threshold) return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return route('files.display', ['path' => ltrim($this->image_path, '/')]) . '?t=' . ($this->updated_at ? $this->updated_at->timestamp : time());
        }
        return null;
    }

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}