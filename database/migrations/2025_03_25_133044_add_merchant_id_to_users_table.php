<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Add merchant_id as a non-nullable foreign key
            // with a default value of 1 (the "global" merchant).
            $table->unsignedBigInteger('merchant_id')->default(1);

            // Create the foreign key constraint
            $table->foreign('merchant_id')
                  ->references('id')
                  ->on('merchants')
                  ->onDelete('cascade'); 

        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Drop the foreign key first, then the column
            $table->dropForeign(['merchant_id']);
            $table->dropColumn('merchant_id');
        });
    }
};
