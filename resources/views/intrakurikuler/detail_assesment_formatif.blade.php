@extends('layouts.master')

@section('title', 'Asesmen Formatif')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Detail Asesmen Formatif" />

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
                            <textarea class="form-control" id="capaianTertinggi"
                                rows="4">Aditya Rizki Arifin membutuhkan bimbingan dalam menganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan)., membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf, </textarea>
                        </div>

                        {{-- TP 1 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 1</label>
                            <p>Membaca Al-Qur’an dengan meyakini bahwa kontrol diri adalah perintah agama.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[1][tercapai]" value="1"
                                    id="tp1_tercapai">
                                <label class="form-check-label" for="tp1_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[1][tampil_rapor]" value="1"
                                    id="tp1_rapor" checked>
                                <label class="form-check-label" for="tp1_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 2 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 2</label>
                            <p>Menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[2][tercapai]" value="1"
                                    id="tp2_tercapai" checked>
                                <label class="form-check-label" for="tp2_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[2][tampil_rapor]" value="1"
                                    id="tp2_rapor" checked>
                                <label class="form-check-label" for="tp2_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 3 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 3</label>
                            <p>Menganalisis Q.S. Al-Anfal/8:72, serta hadits tentang control diri (Mujahadah An-Nafs).</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[3][tercapai]" value="1"
                                    id="tp3_tercapai" checked>
                                <label class="form-check-label" for="tp3_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[3][tampil_rapor]" value="1"
                                    id="tp3_rapor" checked>
                                <label class="form-check-label" for="tp3_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 4 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 4</label>
                            <p>Membaca Q.S. Al-Anfal/8:72, sesuai dengan kaidah tajwid dan Makharijul Huruf.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[4][tercapai]" value="1"
                                    id="tp4_tercapai" checked>
                                <label class="form-check-label" for="tp4_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[4][tampil_rapor]" value="1"
                                    id="tp4_rapor">
                                <label class="form-check-label" for="tp4_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 5 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 5</label>
                            <p>Menghafal Q.S. Al-Anfal/8:72, dengan fasih dan lancar.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[5][tercapai]" value="1"
                                    id="tp5_tercapai">
                                <label class="form-check-label" for="tp5_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[5][tampil_rapor]" value="1"
                                    id="tp5_rapor" checked>
                                <label class="form-check-label" for="tp5_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 6 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 6</label>
                            <p>Menyajikan hubungan antara kualitas keimanan dengan control diri (Mujahadah An-Nafs), sesuai dengan pesan Q.S. Al-Anfal /8:72, serta hadits.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[6][tercapai]" value="1"
                                    id="tp6_tercapai" checked>
                                <label class="form-check-label" for="tp6_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[6][tampil_rapor]" value="1"
                                    id="tp6_rapor" checked>
                                <label class="form-check-label" for="tp6_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 7 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 7</label>
                            <p>Membaca Al-Qur’an dengan meyakini bahwa prasangka baik (husnuzzan), adalah perintah agama.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[7][tercapai]" value="1"
                                    id="tp7_tercapai" checked>
                                <label class="form-check-label" for="tp7_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[7][tampil_rapor]" value="1"
                                    id="tp7_rapor" checked>
                                <label class="form-check-label" for="tp7_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 8 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 8</label>
                            <p>MenganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan).</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[8][tercapai]" value="1"
                                    id="tp8_tercapai" checked>
                                <label class="form-check-label" for="tp8_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[8][tampil_rapor]" value="1"
                                    id="tp8_rapor" checked>
                                <label class="form-check-label" for="tp8_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 9 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 9</label>
                            <p>Membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[9][tercapai]" value="1"
                                    id="tp9_tercapai" checked>
                                <label class="form-check-label" for="tp9_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[9][tampil_rapor]" value="1"
                                    id="tp9_rapor" checked>
                                <label class="form-check-label" for="tp9_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                        {{-- TP 10 --}}
                        <div class="mb-4 col-md-6">
                            <label class="form-label fs-5 f-w-600">TP 10</label>
                            <p>Menghafal Q.S. Al-Hujurat/49:12 dengan fasih dan lancar.</p>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tp[10][tercapai]" value="1"
                                    id="tp10_tercapai" checked>
                                <label class="form-check-label" for="tp10_tercapai">
                                    TP tercapai
                                </label>
                            </div>

                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="tp[10][tampil_rapor]" value="1"
                                    id="tp10_rapor" checked>
                                <label class="form-check-label" for="tp10_rapor">
                                    Tampilkan di rapor
                                </label>
                            </div>
                        </div>

                    </div>
                    <div div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('assesment_formatif.index') }}" class="btn btn-light-secondary">Kembali</a>
                        </div>
                        <div class="d-flex gap-5">
                            <a href="#" class="link-primary"><i class="bi bi-chevron-left"> BABY CANTIKA CAHAYA PERMATA</i></a>
                            <a href="#" class="link-primary">ALYA NUR ZAHRA <i class="bi bi-chevron-right"></i></a>
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