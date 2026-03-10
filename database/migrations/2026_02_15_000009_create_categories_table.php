<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->longText('name');                // translatable JSON
            $table->longText('description')->nullable(); // translatable JSON
            $table->integer('parent_id')->nullable(); // self-referencing
            $table->string('image')->nullable();
            $table->boolean('active');
            $table->boolean('home_page');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
