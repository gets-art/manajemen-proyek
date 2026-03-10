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
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->integer('status');
            $table->double('final_total')->nullable();
            $table->double('paid_total')->nullable();
            $table->double('rest_total')->nullable();
            $table->double('observation')->nullable();
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
