@extends('layouts.master')

@section('title', 'Manajemen Siswa')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
  <x-breadcrumb item="Manajemen Siswa" active="Manajemen Siswa" />

  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="mb-0">Daftar Siswa</h5>
            <span class="d-block m-t-5">
              {{ $kelas_ajar->tahunAjaran->tahun ?? '-' }} {{ $kelas_ajar->tahunAjaran->semester ?? '' }}
              • {{ $kelas_ajar->kelas->nama_kelas ?? '-' }}
            </span>
          </div>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('akademik.siswa.create', $kelas_ajar->kelas_ajar_id) }}" class="btn btn-primary">
              <i class="bi bi-plus-lg"></i> Tambah Siswa
            </a>
          </div>
        </div>

        <div class="card-body table-border-style">
          @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
          @endif

          <div class="table-responsive">
            <table class="table" id="pc-dt-simple">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>NIS / NISN</th>
                  <th>Domisili</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                @forelse ($riwayat as $r)
                  @php($s = $r->siswa)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s?->user?->name ?? $s?->nama ?? '-' }}</td>
                    <td>{{ $s?->user?->username ?? '-' }}</td>
                    <td>{{ $s?->nis ?? '-' }} / {{ $s?->nisn ?? '-' }}</td>
                    <td>
                      {{ $s?->kelurahan?->nama ?? '-' }},
                      {{ $s?->kelurahan?->kecamatan?->nama ?? '-' }}
                    </td>
                    <td>
                      @if($s)
                        <a href="{{ route('akademik.siswa.edit', [$kelas_ajar->kelas_ajar_id, $s->siswa_id]) }}"
                           class="btn btn-sm btn-light-warning mb-1">Edit</a>

                        <form action="{{ route('akademik.siswa.destroy', [$kelas_ajar->kelas_ajar_id, $s->siswa_id]) }}"
                              method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-light-danger mb-1"
                                  onclick="return confirm('Keluarkan siswa dari kelas ini?')">
                            Remove
                          </button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">Belum ada data siswa di kelas ini.</td>
                  </tr>
                @endforelse
              </tbody>

            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script type="module">
    import { DataTable } from '/build/js/plugins/module.js';
    window.dt = new DataTable('#pc-dt-simple');
  </script>
@endsection
