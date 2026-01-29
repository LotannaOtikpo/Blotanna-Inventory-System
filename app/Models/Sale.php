<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;

class Sale extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $fillable = [
        'transaction_id',
        'total_amount',
        'tax_amount',
        'payment_method',
        'user_id',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}