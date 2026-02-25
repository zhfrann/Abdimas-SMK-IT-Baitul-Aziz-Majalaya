<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::table('sekolah', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable();
        });

        Schema::table('sekolah', function (Blueprint $table) {
            $table->foreign('staff_id')
                ->references('staff_id')
                ->on('staff')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->dropColumn(['nama_kepala_sekolah', 'nip_kepala_sekolah']);
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
        });

        Schema::table('sekolah', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable(false)->change();

            $table->foreign('staff_id')
                ->references('staff_id')
                ->on('staff')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('nama_kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah')->nullable();
        });
    }
};