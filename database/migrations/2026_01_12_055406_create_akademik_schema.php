<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // WILAYAH
        // =========================
        Schema::create('provinsi', function (Blueprint $table) {
            $table->string('provinsi_id')->primary();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kabupaten', function (Blueprint $table) {
            $table->string('kabupaten_id')->primary();
            $table->string('provinsi_id');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('provinsi_id')
                ->references('provinsi_id')->on('provinsi')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('kecamatan', function (Blueprint $table) {
            $table->string('kecamatan_id')->primary();
            $table->string('kabupaten_id');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('kabupaten_id')
                ->references('kabupaten_id')->on('kabupaten')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('kelurahan', function (Blueprint $table) {
            $table->string('kelurahan_id')->primary();
            $table->string('kecamatan_id');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('kecamatan_id')
                ->references('kecamatan_id')->on('kecamatan')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        // =========================
        // SEKOLAH
        // =========================
        Schema::create('sekolah', function (Blueprint $table) {
            $table->unsignedInteger('npsn')->primary(); // INT PK
            $table->string('nama_sekolah');
            $table->string('nss')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('kelurahan_id')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('nama_kepala_sekolah')->nullable();
            $table->timestamps();

            $table->foreign('kelurahan_id')
                ->references('kelurahan_id')->on('kelurahan')
                ->cascadeOnUpdate()->nullOnDelete();
        });

        // ========================
        // Staff Sekolah
        // ========================
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id');

            // FK ke users.id
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nip')->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['l', 'p']);
            $table->string('tempat_lahir_kabupaten_id');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('pendidikan_sebelumnya');
            $table->text('alamat');
            $table->string('kelurahan_id');
            $table->timestamps();

            $table->foreign('tempat_lahir_kabupaten_id')
                ->references('kabupaten_id')->on('kabupaten')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('kelurahan_id')
                ->references('kelurahan_id')->on('kelurahan')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        // =========================
        // ORANG TUA (profil)
        // =========================
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id('orang_tua_id');

            // FK ke users.id (bawaan Laravel)
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users') // otomatis -> references('id')->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('pekerjaan_ayah');
            $table->string('pekerjaan_ibu');
            $table->string('jalan');
            $table->string('kelurahan_id');
            $table->timestamps();

            $table->foreign('kelurahan_id')
                ->references('kelurahan_id')->on('kelurahan')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        // =========================
        // SISWA (profil)
        // =========================
        Schema::create('siswa', function (Blueprint $table) {
            $table->id('siswa_id');

            // FK ke users.id
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nis')->unique();
            $table->string('nisn')->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['l', 'p']);
            $table->string('tempat_lahir_kabupaten_id');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('pendidikan_sebelumnya');
            $table->text('alamat');

            // relasi ke orang tua (1 orang tua bisa punya banyak siswa)
            $table->unsignedBigInteger('orang_tua_id');
            $table->string('kelurahan_id');

            $table->timestamps();

            $table->foreign('tempat_lahir_kabupaten_id')
                ->references('kabupaten_id')->on('kabupaten')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('orang_tua_id')
                ->references('orang_tua_id')->on('orang_tua')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('kelurahan_id')
                ->references('kelurahan_id')->on('kelurahan')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        // =========================
        // AKADEMIK MASTER
        // =========================
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->increments('tahun_ajaran_id');
            $table->string('tahun');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->increments('kelas_id');
            $table->string('nama_kelas');
            $table->timestamps();
        });

        Schema::create('kelas_ajar', function (Blueprint $table) {
            $table->increments('kelas_ajar_id');
            $table->unsignedInteger('kelas_id');
            $table->unsignedInteger('tahun_ajaran_id');

            // wali_user_id harus cocok dengan users.id (BIGINT)
            $table->foreignId('wali_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->foreign('kelas_id')
                ->references('kelas_id')->on('kelas')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('tahun_ajaran_id')
                ->references('tahun_ajaran_id')->on('tahun_ajaran')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->unique(['kelas_id', 'tahun_ajaran_id'], 'uniq_kelas_tahun_ajaran');
        });

        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->increments('riwayat_kelas_id');
            $table->unsignedBigInteger('siswa_id');   // siswa.id (BIGINT)
            $table->unsignedInteger('kelas_ajar_id'); // kelas_ajar_id increments
            $table->timestamps();

            $table->foreign('siswa_id')
                ->references('siswa_id')->on('siswa')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('kelas_ajar_id')
                ->references('kelas_ajar_id')->on('kelas_ajar')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['siswa_id', 'kelas_ajar_id'], 'uniq_siswa_kelas_ajar');
        });

        // =========================
        // INTRAKURIKULER
        // =========================
        Schema::create('intrakurikuler', function (Blueprint $table) {
            $table->increments('intrakurikuler_id');
            $table->string('nama_pelajaran');
            $table->unsignedInteger('kelas_ajar_id');

            // pengampu_user_id cocok dengan users.id
            $table->foreignId('pengampu_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->foreign('kelas_ajar_id')
                ->references('kelas_ajar_id')->on('kelas_ajar')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['kelas_ajar_id', 'nama_pelajaran'], 'uniq_mapel_per_kelas_ajar');
        });

        Schema::create('kehadiran_intrakurikuler', function (Blueprint $table) {
            $table->increments('kehadiran_intrakurikuler_id');
            $table->unsignedInteger('intrakurikuler_id');
            $table->unsignedInteger('riwayat_kelas_id');
            $table->unsignedInteger('sakit')->default(0);
            $table->unsignedInteger('izin')->default(0);
            $table->unsignedInteger('absen')->default(0);
            $table->timestamps();

            $table->foreign('intrakurikuler_id')
                ->references('intrakurikuler_id')->on('intrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('riwayat_kelas_id')
                ->references('riwayat_kelas_id')->on('riwayat_kelas')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['intrakurikuler_id', 'riwayat_kelas_id'], 'uniq_kehadiran_intra');
        });

        Schema::create('tujuan_pembelajaran', function (Blueprint $table) {
            $table->increments('tujuan_pembelajaran_id');
            $table->unsignedInteger('intrakurikuler_id');
            $table->text('deskripsi');
            $table->timestamps();

            $table->foreign('intrakurikuler_id')
                ->references('intrakurikuler_id')->on('intrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('asesmen_formatif', function (Blueprint $table) {
            $table->increments('asesmen_formatif_id');
            $table->unsignedInteger('intrakurikuler_id');
            $table->text('deskripsi_catatan_tertinggi');
            $table->text('deskripsi_catatan_terendah');
            $table->timestamps();

            $table->foreign('intrakurikuler_id')
                ->references('intrakurikuler_id')->on('intrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('asesmen_formatif_detail', function (Blueprint $table) {
            $table->increments('asesmen_formatif_detail_id');
            $table->unsignedInteger('asesmen_formatif_id');
            $table->unsignedInteger('tujuan_pembelajaran_id');
            $table->boolean('kktp');
            $table->boolean('tampil');
            $table->timestamps();

            $table->foreign('asesmen_formatif_id')
                ->references('asesmen_formatif_id')->on('asesmen_formatif')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('tujuan_pembelajaran_id')
                ->references('tujuan_pembelajaran_id')->on('tujuan_pembelajaran')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['asesmen_formatif_id', 'tujuan_pembelajaran_id'], 'uniq_formatif_tujuan');
        });

        Schema::create('lingkup_materi', function (Blueprint $table) {
            $table->increments('lingkup_materi_id');
            $table->unsignedInteger('intrakurikuler_id');
            $table->string('nama_materi');
            $table->timestamps();

            $table->foreign('intrakurikuler_id')
                ->references('intrakurikuler_id')->on('intrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('asesmen_sumatif', function (Blueprint $table) {
            $table->increments('asesmen_sumatif_id');
            $table->unsignedInteger('intrakurikuler_id');
            $table->unsignedInteger('tahun_ajaran_id');
            $table->enum('asesmen_type', ['sumatif_lingkup', 'test', 'non_test', 'sas']);
            $table->unsignedInteger('lingkup_materi_id')->nullable();
            $table->unsignedInteger('asesmen_no')->nullable();
            $table->timestamps();

            $table->foreign('intrakurikuler_id')
                ->references('intrakurikuler_id')->on('intrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('tahun_ajaran_id')
                ->references('tahun_ajaran_id')->on('tahun_ajaran')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('lingkup_materi_id')
                ->references('lingkup_materi_id')->on('lingkup_materi')
                ->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::create('skor_asesmen_siswa', function (Blueprint $table) {
            $table->increments('skor_asesmen_siswa_id');
            $table->unsignedInteger('riwayat_kelas_id');
            $table->unsignedInteger('asesmen_sumatif_id');
            $table->integer('nilai')->nullable();
            $table->unsignedInteger('tahun_ajaran_id');
            $table->timestamps();

            $table->foreign('riwayat_kelas_id')
                ->references('riwayat_kelas_id')->on('riwayat_kelas')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('asesmen_sumatif_id')
                ->references('asesmen_sumatif_id')->on('asesmen_sumatif')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('tahun_ajaran_id')
                ->references('tahun_ajaran_id')->on('tahun_ajaran')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->unique(['riwayat_kelas_id', 'asesmen_sumatif_id'], 'uniq_skor_per_asesmen');
        });

        // =========================
        // EKSTRAKURIKULER
        // =========================
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->increments('ekstrakurikuler_id');
            $table->string('nama_pelajaran');
            $table->unsignedInteger('tahun_ajaran_id');

            // user_id (pembina) refer ke users.id
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->foreign('tahun_ajaran_id')
                ->references('tahun_ajaran_id')->on('tahun_ajaran')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('kehadiran_ekstrakurikuler', function (Blueprint $table) {
            $table->increments('kehadiran_ekstrakurikuler_id');
            $table->unsignedInteger('ekstrakurikuler_id');
            $table->unsignedInteger('sakit')->default(0);
            $table->unsignedInteger('izin')->default(0);
            $table->unsignedInteger('absen')->default(0);
            $table->timestamps();

            $table->foreign('ekstrakurikuler_id')
                ->references('ekstrakurikuler_id')->on('ekstrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('siswa_ekstrakurikuler', function (Blueprint $table) {
            $table->increments('siswa_ekstrakurikuler_id');
            $table->unsignedInteger('riwayat_kelas_id');
            $table->unsignedInteger('ekstrakurikuler_id');
            $table->timestamps();

            $table->foreign('riwayat_kelas_id')
                ->references('riwayat_kelas_id')->on('riwayat_kelas')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('ekstrakurikuler_id')
                ->references('ekstrakurikuler_id')->on('ekstrakurikuler')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['riwayat_kelas_id', 'ekstrakurikuler_id'], 'uniq_siswa_ekskul');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_ekstrakurikuler');
        Schema::dropIfExists('kehadiran_ekstrakurikuler');
        Schema::dropIfExists('ekstrakurikuler');

        Schema::dropIfExists('skor_asesmen_siswa');
        Schema::dropIfExists('asesmen_sumatif');
        Schema::dropIfExists('lingkup_materi');
        Schema::dropIfExists('asesmen_formatif_detail');
        Schema::dropIfExists('asesmen_formatif');
        Schema::dropIfExists('tujuan_pembelajaran');
        Schema::dropIfExists('kehadiran_intrakurikuler');
        Schema::dropIfExists('intrakurikuler');

        Schema::dropIfExists('riwayat_kelas');
        Schema::dropIfExists('kelas_ajar');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tahun_ajaran');

        Schema::dropIfExists('siswa');
        Schema::dropIfExists('orang_tua');
        Schema::dropIfExists('sekolah');

        Schema::dropIfExists('kelurahan');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('kabupaten');
        Schema::dropIfExists('provinsi');
    }
};
