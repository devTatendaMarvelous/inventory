<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('ware_houses')->nullOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('ware_houses')->nullOnDelete();
            $table->enum('movement_type', ['IN', 'OUT', 'TRANSFER']);
            $table->decimal('quantity_in', 15, 2)->default(0.00);
            $table->decimal('quantity_Out', 15, 2)->default(0.00);
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->mediumText('notes')->nullable();
            $table->foreignId('initiated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('validated_by')->nullable() ->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
