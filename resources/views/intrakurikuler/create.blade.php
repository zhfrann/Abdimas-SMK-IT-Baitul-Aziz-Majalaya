@extends('layouts.master')

@section('title', 'Tambah Intrakurikuler')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
  <x-breadcrumb item="Intrakurikuler" active="Tambah Intrakurikuler" />

  <div class="container">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Tambah Intrakurikuler</h5>
          <span class="d-block m-t-5">Guru hanya boleh menjadi pengampu 1x untuk setiap tahun ajaran</span>
        </div>

        <div class="card-body">
          <form action="{{ route('intrakurikuler.store') }}" method="POST">
            @csrf

            <div class="mb-3">
              <label>Mata Pelajaran</label>
              <input type="text" name="nama_pelajaran"
                     value="{{ old('nama_pelajaran') }}"
                     class="form-control @error('nama_pelajaran') is-invalid @enderror">
              @error('nama_pelajaran')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-3">
              <label>Kelas Ajar</label>
              <select id="kelas_ajar_id" name="kelas_ajar_id"
                      class="form-control @error('kelas_ajar_id') is-invalid @enderror">
                <option value="">Pilih Kelas Ajar</option>
                @foreach ($kelasAjar as $ka)
                  <option
                    value="{{ $ka->kelas_ajar_id }}"
                    data-tahun="{{ $ka->tahun_ajaran_id }}"
                    {{ old('kelas_ajar_id') == $ka->kelas_ajar_id ? 'selected' : '' }}
                  >
                    {{ $ka->tahunAjaran->tahun }} {{ $ka->tahunAjaran->semester }} • {{ $ka->kelas->nama_kelas }}
                  </option>
                @endforeach
              </select>
              @error('kelas_ajar_id')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
              <small class="text-muted d-block mt-1">Pilih kelas ajar untuk menentukan tahun ajaran.</small>
            </div>

            <div class="mb-3">
              <label>Guru Pengampu</label>
              <select id="pengampu_user_id" name="pengampu_user_id"
                      class="form-control @error('pengampu_user_id') is-invalid @enderror" disabled>
                <option value="">Pilih kelas ajar dulu...</option>
              </select>
              @error('pengampu_user_id')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
              <small class="text-muted d-block mt-1">
                Guru yang sudah menjadi pengampu intrakurikuler pada tahun ajaran yang sama tidak akan ditampilkan.
              </small>
            </div>

            {{-- Ini tempat nyimpen data JSON biar JS-nya pure --}}
            <div
              id="intrakurikuler-data"
              data-old-guru='@json(old("pengampu_user_id"))'
              data-all-guru='@json(
                $guru->map(fn($g) => [
                  "id" => $g->id,
                  "label" => ($g->staff?->nama ?? $g->name) . ($g->staff?->nip ? " - NIP: ".$g->staff->nip : ""),
                ])->values()
              )'
              data-used-by-tahun='@json($usedGuruByTahunAjaran)'>
            </div>

            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('intrakurikuler.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>

      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  const kelasSelect = document.getElementById('kelas_ajar_id');
  const guruSelect  = document.getElementById('pengampu_user_id');

  const dataEl = document.getElementById('intrakurikuler-data');

  const oldGuruId = JSON.parse(dataEl.dataset.oldGuru ?? 'null');
  const allGuru = JSON.parse(dataEl.dataset.allGuru ?? '[]');
  const usedGuruByTahun = JSON.parse(dataEl.dataset.usedByTahun ?? '{}');

  function renderGuruOptions() {
    guruSelect.innerHTML = '';
    guruSelect.disabled = true;

    const selectedOption = kelasSelect.options[kelasSelect.selectedIndex];
    const tahunId = selectedOption?.dataset?.tahun;

    if (!kelasSelect.value || !tahunId) {
      guruSelect.innerHTML = `<option value="">Pilih kelas ajar dulu...</option>`;
      return;
    }

    const used = new Set((usedGuruByTahun[tahunId] || []).map(Number));
    const available = allGuru.filter(g => !used.has(Number(g.id)));

    if (available.length === 0) {
      guruSelect.innerHTML = `<option value="">Tidak ada guru tersedia untuk tahun ajaran ini</option>`;
      return;
    }

    guruSelect.innerHTML = `<option value="">Pilih Guru</option>`;
    available.forEach(g => {
      const opt = document.createElement('option');
      opt.value = g.id;
      opt.textContent = g.label;
      if (oldGuruId && Number(oldGuruId) === Number(g.id)) opt.selected = true;
      guruSelect.appendChild(opt);
    });

    guruSelect.disabled = false;
  }

  kelasSelect.addEventListener('change', renderGuruOptions);

  // auto render saat balik dari validation error
  if (kelasSelect.value) renderGuruOptions();
</script>
@endsection
