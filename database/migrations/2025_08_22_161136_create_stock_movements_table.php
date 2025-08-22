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
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('warehouse_id')->references('id')->on('ware_houses');
            $table->enum('movement_type', ['IN', 'OUT','TRANSFER'])->default('IN');
            $table->float('quantity')->default(1.0);
            $table->float('price');
            $table->enum('status', ['APPROVED','REJECTED', 'PENDING'])->default('PENDING');
            $table->foreignId('initiated_by')->references('id')->on('users');
            $table->foreignId('validated_by')->references('id')->on('users');
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
