<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->string('status')->default('Verified')->after('amount_paid'); // Verified, Pending, Rejected
            $table->string('screenshot')->nullable()->after('transaction_reference');
        });

        // Set all existing payments status to 'Verified'
        DB::table('fee_payments')->update(['status' => 'Verified']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn(['status', 'screenshot']);
        });
    }
};
