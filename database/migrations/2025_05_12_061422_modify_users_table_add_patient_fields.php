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
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_type')->nullable()->after('email');
            $table->string('id_no')->nullable()->after('id_type');
            $table->string('gender')->nullable()->after('id_no');
            $table->date('dob')->nullable()->after('gender');
            $table->text('address')->nullable()->after('dob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_type', 'id_no', 'gender', 'dob', 'address']);
        });
    }
};
