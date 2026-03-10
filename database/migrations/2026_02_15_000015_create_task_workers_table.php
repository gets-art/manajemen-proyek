<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_workers', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id');
            $table->integer('worker_id');
            $table->double('paid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_workers');
    }
};
