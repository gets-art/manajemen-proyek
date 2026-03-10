<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id');
            $table->integer('task_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->double('unit_price');
            $table->double('total');
            $table->double('discount');
            $table->double('final_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_tasks');
    }
};
