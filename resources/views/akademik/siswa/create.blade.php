@extends('layouts.master')

@section('title', 'Tambah Siswa')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
  <x-breadcrumb item="Manajemen Siswa" active="Tambah Siswa" />

  <div class="container">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Tambah Siswa</h5>
          <span class="d-block m-t-5">Buat akun login dan profil siswa</span>
        </div>

        <div class="card-body">
          <form action="{{ route('akademik.siswa.store', $kelas_ajar->kelas_ajar_id) }}" method="POST">
            @csrf

            <h6 class="mb-3">Akun Login</h6>

            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="name" value="{{ old('name') }}"
                     class="form-control @error('name') is-invalid @enderror">
              @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-3">
              <label>Username</label>
              <input type="text" name="username" value="{{ old('username') }}"
                     class="form-control @error('username') is-invalid @enderror">
              @error('username')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-3">
              <label>Email (opsional)</label>
              <input type="email" name="email" value="{{ old('email') }}"
                     class="form-control @error('email') is-invalid @enderror">
              @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password"
                     class="form-control @error('password') is-invalid @enderror">
              @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-3">
              <label>Konfirmasi Password</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>

            <hr class="my-4">

            <h6 class="mb-3">Profil Siswa</h6>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}"
                       class="form-control @error('nis') is-invalid @enderror">
                @error('nis')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label>NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}"
                       class="form-control @error('nisn') is-invalid @enderror">
                @error('nisn')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                  <option value="">Pilih</option>
                  <option value="l" {{ old('jenis_kelamin') == 'l' ? 'selected' : '' }}>Laki-laki</option>
                  <option value="p" {{ old('jenis_kelamin') == 'p' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                       class="form-control @error('tanggal_lahir') is-invalid @enderror">
                @error('tanggal_lahir')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Agama</label>
                <input type="text" name="agama" value="{{ old('agama') }}"
                       class="form-control @error('agama') is-invalid @enderror">
                @error('agama')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="mb-3">
              <label>Pendidikan Sebelumnya</label>
              <input type="text" name="pendidikan_sebelumnya" value="{{ old('pendidikan_sebelumnya') }}"
                     class="form-control @error('pendidikan_sebelumnya') is-invalid @enderror">
              @error('pendidikan_sebelumnya')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-3">
              <label>Alamat</label>
              <textarea name="alamat" rows="3"
                        class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
              @error('alamat')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Orang Tua</label>
                <select name="orang_tua_id" class="form-control @error('orang_tua_id') is-invalid @enderror">
                  <option value="">Pilih Orang Tua</option>
                  @foreach ($orangTua as $ot)
                    <option value="{{ $ot->orang_tua_id }}" {{ old('orang_tua_id') == $ot->orang_tua_id ? 'selected' : '' }}>
                      {{ $ot->nama_ayah }} / {{ $ot->nama_ibu }}
                    </option>
                  @endforeach
                </select>
                @error('orang_tua_id')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-success">Simpan</button>
              <a href="{{ route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id) }}" class="btn btn-light">Batal</a>
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
