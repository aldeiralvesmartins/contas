<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Adicionar campo transaction_date se não existir
            if (!Schema::hasColumn('transactions', 'transaction_date')) {
                $table->timestamp('transaction_date')->nullable()->after('category_id');
            }

            // Adicionar campo notes se não existir
            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('transaction_date');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_date', 'notes']);
        });
    }
};
