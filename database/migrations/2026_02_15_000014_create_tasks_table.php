<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->integer('category_id')->nullable();
            $table->longText('name');                    // translatable JSON
            $table->longText('description')->nullable(); // translatable JSON
            $table->decimal('final_total', 15, 2)->nullable();
            $table->decimal('rest_total', 15, 2)->nullable();
            $table->decimal('paid_total', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('Pending');
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
