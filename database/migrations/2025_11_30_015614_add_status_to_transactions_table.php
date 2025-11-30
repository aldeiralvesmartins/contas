<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid'])->default('pending')->after('type');
            $table->date('due_date')->nullable()->after('status');
            $table->date('paid_at')->nullable()->after('due_date');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'due_date', 'paid_at']);
        });
    }
};
