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
        Schema::table('project_budgets', function (Blueprint $table) {
            $table->string('group')->nullable()->after('project_id'); // e.g. PEKERJAAN STRUKTUR
            $table->string('subgroup')->nullable()->after('group'); // e.g. Struktur Beton
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('contractor_fee_percentage', 5, 2)->default(10.00)->after('budget');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('project_budget_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->decimal('contract_amount', 15, 2)->nullable()->after('description'); // The agreed pay for the worker (sisa tagihan starts here)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_budget_id']);
            $table->dropColumn(['project_budget_id', 'contract_amount']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('contractor_fee_percentage');
        });

        Schema::table('project_budgets', function (Blueprint $table) {
            $table->dropColumn(['group', 'subgroup']);
        });
    }
};
