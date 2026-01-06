@extends('layouts.master')

@section('title', 'Asesmen Formatif')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Detail Asesmen Formatif"/>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="fs-4">ADITYA RIZKI ARIFIN</h5>
                    <span class="d-block m-t-5"></span>
                </div>
                <div class="card-body">
                <form>
                  <div class="row">

                    <div class="mb-4 col-md-6">
                        <label class="form-label fs-5 f-w-600">Deskripsi Capaian Tertinggi dalam Rapor</label>
                        <textarea class="form-control" id="capaianTertinggi" rows="4">Aditya Rizki Arifin menunjukkan pemahaman dalam membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama, menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait., 
                        </textarea>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label fs-5 f-w-600">Deskripsi Capaian Terendah dalam Rapor</label>
                        <textarea class="form-control" id="capaianTertinggi" rows="4">Aditya Rizki Arifin membutuhkan bimbingan dalam menganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan)., membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf, </textarea>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 1</label>
                        <p class="min-h-10">Membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp1">
                            <label class="form-check-label" for="tp1">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 2</label>
                        <p>Menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait.</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp2">
                            <label class="form-check-label" for="tp2">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 3</label>
                        <p>Menganalisis Q.S. Al-Anfal/8:72, serta hadits tentang control diri (Mujahadah An-Nafs).</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp3">
                            <label class="form-check-label" for="tp3">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 4</label>
                        <p>Membaca Q.S. Al-Anfal/8:72, sesuai dengan kaidah tajwid dan Makharijul Huruf.</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp4">
                            <label class="form-check-label" for="tp4">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 5</label>
                        <p>Menghafal Q.S. Al-Anfal/8:72, dengan fasih dan lancar.</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp5">
                            <label class="form-check-label" for="tp5">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 6</label>
                        <p>Menyajikan hubungan antara kualitas keimanan dengan control diri (Mujahadah An-Nafs), sesuai dengan pesan Q.S. Al-Anfal /8:72, serta hadits.</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp6">
                            <label class="form-check-label" for="tp6">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 7</label>
                        <p>Membaca Al-Qur’an dengan meyakini bahwa prasangka baik (husnuzzan), adalah perintah agama.</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 7">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp7">
                            <label class="form-check-label" for="tp7">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 8</label>
                        <p>Menganalisis Q.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan).</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 8">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp8">
                            <label class="form-check-label" for="tp8">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 9</label>
                        <p>Membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 9">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp9">
                            <label class="form-check-label" for="tp9">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label fs-5 f-w-600">TP 10</label>
                        <p>Menghafal Q.S. Al-Hujurat/49:12 dengan fasih dan lancar.</p>
                        <input type="number" class="form-control mb-1" placeholder="Capaian TP 10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tp10">
                            <label class="form-check-label" for="tp10">
                            Tampilkan di rapor
                            </label>
                        </div>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="/components/asesmen_formatif" class="btn btn-light-secondary">Kembali</a>
                    </div>
                  </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <script type="module">
      import { DataTable } from '/build/js/plugins/module.js';
      window.dt = new DataTable('#pc-dt-simple');
    </script>
    <!-- [Page Specific JS] end -->
@endsection