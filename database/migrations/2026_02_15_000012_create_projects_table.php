<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->longText('name');                    // translatable JSON
            $table->longText('description')->nullable(); // translatable JSON
            $table->integer('client_id');
            $table->integer('category_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('status');
            $table->decimal('final_total', 15, 2)->nullable();
            $table->decimal('paid_total', 15, 2)->nullable();
            $table->decimal('rest_total', 15, 2)->nullable();
            $table->decimal('observation', 15, 2)->nullable();
            $table->string('image')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
