<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        
        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'business_name', 'value' => 'Blotanna Nig Ltd'],
            ['key' => 'low_stock_threshold', 'value' => '10'],
            ['key' => 'currency_symbol', 'value' => '$'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};