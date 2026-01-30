<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas_ajar', function (Blueprint $table) {
            // KKM per kelas_ajar (per tahun ajaran/semester)
            // default 75 biar data lama aman
            $table->unsignedSmallInteger('kkm')
                ->default(75)
                ->after('wali_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('kelas_ajar', function (Blueprint $table) {
            $table->dropColumn('kkm');
        });
    }
};
