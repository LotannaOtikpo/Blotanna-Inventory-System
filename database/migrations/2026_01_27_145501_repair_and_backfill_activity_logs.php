<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure Table Exists
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action'); 
                $table->text('description')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        // 2. Backfill History (Only if log is empty to prevent duplicates)
        if (DB::table('activity_logs')->count() === 0) {
            
            // Get Admin ID for attribution
            $adminId = DB::table('users')->orderBy('id')->value('id');

            // Backfill Products
            $products = DB::table('products')->get();
            foreach($products as $product) {
                DB::table('activity_logs')->insert([
                    'user_id' => $adminId,
                    'action' => 'Created Product',
                    'description' => "Added product: {$product->name} (Initial Stock: {$product->quantity})",
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]);
            }

            // Backfill Customers
            $customers = DB::table('customers')->get();
            foreach($customers as $customer) {
                DB::table('activity_logs')->insert([
                    'user_id' => $adminId,
                    'action' => 'Created Customer',
                    'description' => "Added customer: {$customer->name}",
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at,
                ]);
            }

            // Backfill Sales
            $sales = DB::table('sales')->get();
            foreach($sales as $sale) {
                DB::table('activity_logs')->insert([
                    'user_id' => $sale->user_id ?? $adminId,
                    'action' => 'New Sale',
                    'description' => "Processed sale {$sale->transaction_id} Amount: {$sale->total_amount}",
                    'created_at' => $sale->created_at,
                    'updated_at' => $sale->updated_at,
                ]);
            }

            // Backfill Invoices
            $invoices = DB::table('invoices')->get();
            foreach($invoices as $invoice) {
                DB::table('activity_logs')->insert([
                    'user_id' => $adminId,
                    'action' => 'Created Invoice',
                    'description' => "Generated invoice #{$invoice->invoice_number}",
                    'created_at' => $invoice->created_at,
                    'updated_at' => $invoice->updated_at,
                ]);
            }

            // Initial System Setup Log
            DB::table('activity_logs')->insert([
                'user_id' => $adminId,
                'action' => 'System Setup',
                'description' => "System installation and initial configuration.",
                'created_at' => now()->subMonths(1), // Backdate
                'updated_at' => now()->subMonths(1),
            ]);
        }
    }

    public function down(): void
    {
        // We generally don't want to drop the table in a repair migration down() 
        // unless we are sure, but for safety we'll leave it or drop if strictly needed.
        // Schema::dropIfExists('activity_logs'); 
    }
};