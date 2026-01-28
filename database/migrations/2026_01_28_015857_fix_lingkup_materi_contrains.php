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
        /**
         * 1) LINGKUP MATERI: cegah duplikat dalam 1 intrakurikuler
         */
        Schema::table('lingkup_materi', function (Blueprint $table) {
            // pastikan belum ada index/unique dengan nama sama
            $table->unique(['intrakurikuler_id', 'nama_materi'], 'uniq_lingkup_per_intra');
        });

        /**
         * 2) ASESMEN SUMATIF -> LINGKUP MATERI:
         * ubah nullOnDelete menjadi restrictOnDelete
         * agar tidak bisa delete lingkup materi kalau masih dipakai
         */
        Schema::table('asesmen_sumatif', function (Blueprint $table) {
            // drop foreign key lama
            $table->dropForeign(['lingkup_materi_id']);
        });

        Schema::table('asesmen_sumatif', function (Blueprint $table) {
            $table->foreign('lingkup_materi_id')
                ->references('lingkup_materi_id')
                ->on('lingkup_materi')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        /**
         * 3) (Opsional tapi bagus) tambah index yang sering dipakai query
         * Kalau kamu yakin MySQL sudah bikin index FK, ini boleh di-skip.
         */
        Schema::table('intrakurikuler', function (Blueprint $table) {
            $table->index('kelas_ajar_id', 'idx_intra_kelas_ajar');
            $table->index('pengampu_user_id', 'idx_intra_pengampu');
        });

        Schema::table('lingkup_materi', function (Blueprint $table) {
            $table->index('intrakurikuler_id', 'idx_lingkup_intra');
        });

        Schema::table('asesmen_sumatif', function (Blueprint $table) {
            $table->index('intrakurikuler_id', 'idx_sumatif_intra');
            $table->index('tahun_ajaran_id', 'idx_sumatif_tahun');
        });
    }

    public function down(): void
    {
        // rollback index opsional
        Schema::table('asesmen_sumatif', function (Blueprint $table) {
            $table->dropIndex('idx_sumatif_intra');
            $table->dropIndex('idx_sumatif_tahun');

            // drop FK restrict yang baru
            $table->dropForeign(['lingkup_materi_id']);
        });

        // balikin FK jadi nullOnDelete seperti semula
        Schema::table('asesmen_sumatif', function (Blueprint $table) {
            $table->foreign('lingkup_materi_id')
                ->references('lingkup_materi_id')
                ->on('lingkup_materi')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('lingkup_materi', function (Blueprint $table) {
            $table->dropIndex('idx_lingkup_intra');
            $table->dropUnique('uniq_lingkup_per_intra');
        });

        Schema::table('intrakurikuler', function (Blueprint $table) {
            $table->dropIndex('idx_intra_kelas_ajar');
            $table->dropIndex('idx_intra_pengampu');
        });
    }
};
