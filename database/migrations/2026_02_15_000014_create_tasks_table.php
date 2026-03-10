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
            $table->double('final_total')->nullable();
            $table->double('rest_total')->nullable();
            $table->double('paid_total')->nullable();
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
