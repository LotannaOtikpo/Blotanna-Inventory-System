<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address'
    ];

    /**
     * Boot the model.
     * Prevents writes if the table does not exist to avoid application crashes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            try {
                if (!Schema::hasTable('activity_logs')) {
                    return false; // Cancel save
                }
            } catch (\Exception $e) {
                // If DB connection fails entirely, we log it and cancel save
                Log::error('ActivityLog Error: ' . $e->getMessage());
                return false;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getActionColorAttribute()
    {
        $action = strtolower($this->action);
        if (str_contains($action, 'delete') || str_contains($action, 'remove')) return 'text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400';
        if (str_contains($action, 'create') || str_contains($action, 'add') || str_contains($action, 'restore')) return 'text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400';
        if (str_contains($action, 'update') || str_contains($action, 'edit')) return 'text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400';
        if (str_contains($action, 'login')) return 'text-purple-600 bg-purple-50 dark:bg-purple-900/20 dark:text-purple-400';
        return 'text-gray-600 bg-gray-50 dark:bg-gray-800 dark:text-gray-400';
    }
}