@extends('layouts.master')

@section('title', 'Detail Asesmen Sumatif')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Detail Asesmen Sumatif" />

<div class="row">
  <div class="col-md-12">
    <div class="card">

      {{-- Header --}}
      <div class="card-header">
        <h5 class="fs-4 mb-0">ADITYA RIZKI ARIFIN</h5>
        <span class="d-block m-t-5">Pendidikan Agama Islam dan Budi Pekerti • Kelas 12</span>
      </div>

      <div class="card-body">
        <form>

          <div class="row">

            {{-- ===================== CONTAINER 1: SUMATIF AKHIR LINGKUP MATERI ===================== --}}
            <div class="col-lg-6 mb-3">
              <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <div>
                    <h5 class="mb-0">Sumatif Akhir Lingkup Materi</h5>
                    <span class="d-block m-t-5">Isi nilai per lingkup materi</span>
                  </div>
                  {{-- total dummy --}}
                  <span class="badge bg-light-primary">Total: 86</span>
                </div>

                <div class="card-body">
                  <div class="row">

                    {{-- LM 1 --}}
                    <div class="mb-4 col-md-6">
                      <label class="form-label fs-6 f-w-600">LM 1</label>
                      <p class="mb-2 text-muted">
                        Membaca Al-Qur’an dengan meyakini bahwa kontrol diri adalah perintah agama.
                      </p>
                      <input type="number" class="form-control" placeholder="Nilai (0-100)" value="85">
                    </div>

                    {{-- LM 2 --}}
                    <div class="mb-4 col-md-6">
                      <label class="form-label fs-6 f-w-600">LM 2</label>
                      <p class="mb-2 text-muted">
                        Menunjukan perilaku control diri (Mujahadah An-Nafs) sebagai implementasi Q.S. Al-Anfal.
                      </p>
                      <input type="number" class="form-control" placeholder="Nilai (0-100)" value="88">
                    </div>

                    {{-- LM 3 --}}
                    <div class="mb-4 col-md-6">
                      <label class="form-label fs-6 f-w-600">LM 3</label>
                      <p class="mb-2 text-muted">
                        Menganalisis Q.S. Al-Anfal/8:72 serta hadits tentang control diri.
                      </p>
                      <input type="number" class="form-control" placeholder="Nilai (0-100)" value="82">
                    </div>

                    {{-- LM 4 --}}
                    <div class="mb-4 col-md-6">
                      <label class="form-label fs-6 f-w-600">LM 4</label>
                      <p class="mb-2 text-muted">
                        Membaca ayat sesuai kaidah tajwid dan makharijul huruf.
                      </p>
                      <input type="number" class="form-control" placeholder="Nilai (0-100)" value="86">
                    </div>

                  </div>
                </div>
              </div>
            </div>

            {{-- ===================== CONTAINER 2: SUMATIF AKHIR SEMESTER ===================== --}}
            <div class="col-lg-6 mb-3">
              <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <div>
                    <h5 class="mb-0">Sumatif Akhir Semester</h5>
                    <span class="d-block m-t-5">Isi nilai komponen akhir semester</span>
                  </div>
                  {{-- total dummy --}}
                  <span class="badge bg-light-primary">Total: 82</span>
                </div>

                <div class="card-body">
                  <div class="row">

                    {{-- SAS 1 --}}
                    <div class="mb-4 col-md-6">
                      <label class="form-label fs-6 f-w-600">Non test</label>
                      <p class="mb-2 text-muted">Nilai akhir non test</p>
                      <input type="number" class="form-control" placeholder="Nilai (0-100)" value="80">
                    </div>

                    {{-- SAS 2 --}}
                    <div class="mb-4 col-md-6">
                      <label class="form-label fs-6 f-w-600">Test</label>
                      <p class="mb-2 text-muted">Nilai akhir test</p>
                      <input type="number" class="form-control" placeholder="Nilai (0-100)" value="84">
                    </div>

                  </div>
                </div>
              </div>
            </div>

            {{-- ===================== RINGKASAN TOTAL (DUMMY TANPA LOGIC) ===================== --}}
            <div class="col-12">
              <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">

                  <div class="d-flex flex-wrap gap-3">
                    <div class="px-3 py-2 border rounded">
                      <div class="text-muted small">Total Akhir Lingkup Materi</div>
                      <div class="fs-5 f-w-600">86</div>
                    </div>

                    <div class="px-3 py-2 border rounded">
                      <div class="text-muted small">Total Akhir Semester</div>
                      <div class="fs-5 f-w-600">82</div>
                    </div>

                    <div class="px-3 py-2 border rounded">
                      <div class="text-muted small">Nilai Rapor</div>
                      <div class="fs-4 f-w-700 text-primary">84</div>
                    </div>
                  </div>



                </div>
              </div>
            </div>

            {{-- ===================== NAV SISWA (TAMPILAN SAJA) ===================== --}}
            <div class="d-flex align-items-center gap-2">
              <button type="submit" class="btn btn-primary">Simpan</button>
              <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-light-secondary">Kembali</a>
            </div>
            <div class="col-12">
              <div class="d-flex justify-content-end gap-5 mt-1">
                <a href="#" class="link-primary">
                  <i class="bi bi-chevron-left"></i> BABY CANTIKA CAHAYA PERMATA
                </a>
                <a href="#" class="link-primary">
                  ALYA NUR ZAHRA <i class="bi bi-chevron-right"></i>
                </a>
              </div>
            </div>

          </div>
        </form>
      </div>

    </div>
  </div>
</div>

@endsection


@section('scripts')
<script type="module">
  import {
    DataTable
  } from '/build/js/plugins/module.js';
  window.dt = new DataTable('#pc-dt-simple');
</script>
@endsection