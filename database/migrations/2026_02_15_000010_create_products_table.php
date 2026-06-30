<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->longText('name');                    // translatable JSON
            $table->longText('description')->nullable(); // translatable JSON
            $table->decimal('price', 15, 2);
            $table->integer('category_id');
            $table->string('image');
            $table->integer('stock')->default(0);
            $table->boolean('active');
            $table->boolean('featured');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
