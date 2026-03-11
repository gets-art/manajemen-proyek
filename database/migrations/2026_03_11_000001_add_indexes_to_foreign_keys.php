<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->index('parent_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('category_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('category_id');
        });

        Schema::table('task_workers', function (Blueprint $table) {
            $table->unique(['task_id', 'worker_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_method_id');
            $table->index(['paymentable_type', 'paymentable_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_category_id');
            $table->index('project_id');
            $table->index('payment_method_id');
            $table->index('added_by');
        });

        Schema::table('purchase_tasks', function (Blueprint $table) {
            $table->index('supplier_id');
            $table->index('task_id');
            $table->index('product_id');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->index(['imageable_type', 'imageable_id']);
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('task_workers', function (Blueprint $table) {
            $table->dropUnique(['task_id', 'worker_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_method_id']);
            $table->dropIndex(['paymentable_type', 'paymentable_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_category_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['payment_method_id']);
            $table->dropIndex(['added_by']);
        });

        Schema::table('purchase_tasks', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['task_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex(['imageable_type', 'imageable_id']);
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
