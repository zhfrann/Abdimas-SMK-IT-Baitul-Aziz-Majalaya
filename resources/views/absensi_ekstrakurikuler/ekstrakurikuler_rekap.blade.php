@extends('layouts.master')

@section('title', 'Rekap Absensi (Ekstrakurikuler)')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
  <link rel="stylesheet" href="/build/css/plugins/flatpickr.min.css" />
@endsection

@section('content')
  <x-breadcrumb item="Absensi" active="Rekap Absensi (Ekstrakurikuler)" />

  <div class="row">
    <div class="col-12">
      <div class="card table-card">
        <div class="card-header">
          <div class="d-sm-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-1">Rekap Absensi - {{ $ekstrakurikuler->nama_pelajaran }}</h5>
              <small class="text-muted">
                {{ $ekstrakurikuler->tahunAjaran?->tahun ?? '-' }} {{ $ekstrakurikuler->tahunAjaran?->semester ?? '' }}
                • Pembina: {{ $ekstrakurikuler->pembina?->name ?? '-' }}
                • Read-only
              </small>
            </div>

            <div class="d-flex gap-2">
              <a href="{{ route('absensi.ekstrakurikuler.harian', $ekstrakurikuler->ekstrakurikuler_id) }}"
                 class="btn btn-outline-secondary">
                Absen Harian
              </a>
            </div>
          </div>

          <div class="row g-2 align-items-end mt-3">
            <div class="col-12 col-md-4">
              <label class="form-label mb-1">Range Tanggal</label>
              <input type="text" id="rangeDate" class="form-control" placeholder="Pilih range tanggal" autocomplete="off" />
              <input type="hidden" id="dateFrom" value="{{ $from }}">
              <input type="hidden" id="dateTo" value="{{ $to }}">
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label mb-1">Preset</label>
              <select id="presetRange" class="form-select">
                <option value="">Custom</option>
                <option value="this_month">Bulan ini</option>
                <option value="last_30">30 hari terakhir</option>
              </select>
            </div>

            <div class="col-12 col-md-5 d-flex gap-2">
              <button type="button" id="btnApply" class="btn btn-primary">Terapkan</button>
              <a href="{{ route('absensi.ekstrakurikuler.rekap', $ekstrakurikuler->ekstrakurikuler_id) }}"
                 class="btn btn-outline-secondary">
                Reset
              </a>
            </div>
          </div>
        </div>

        <div class="card-body pt-3">
          <div class="table-responsive">
            <table class="table table-hover" id="pc-dt-simple">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Total Hadir</th>
                  <th>Total Alpha</th>
                  <th>Total Sakit</th>
                  <th>Total Izin</th>
                  <th>Persentase Hadir</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($rows as $r)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <img src="{{ $r['avatar'] ?? '/build/images/user/avatar-1.jpg' }}"
                               alt="user" class="img-radius wid-40" />
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-0">{{ $r['name'] ?? '-' }}</h6>
                          <small class="text-muted">{{ $ekstrakurikuler->nama_pelajaran }}</small>
                        </div>
                      </div>
                    </td>
                    <td>{{ (int)($r['hadir'] ?? 0) }}</td>
                    <td>{{ (int)($r['alpha'] ?? 0) }}</td>
                    <td>{{ (int)($r['sakit'] ?? 0) }}</td>
                    <td>{{ (int)($r['izin'] ?? 0) }}</td>
                    <td><span class="badge bg-light-primary">{{ $r['persen'] ?? 0 }}%</span></td>
                  </tr>
                @endforeach
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

  <script src="/build/js/plugins/flatpickr.min.js"></script>
  <script>
    const rangeInput = document.getElementById('rangeDate');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const preset = document.getElementById('presetRange');
    const btnApply = document.getElementById('btnApply');

    const fp = flatpickr(rangeInput, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      defaultDate: [dateFrom.value || null, dateTo.value || null].filter(Boolean),
      maxDate: 'today',
      onChange: function(selectedDates) {
        if (selectedDates.length >= 2) {
          dateFrom.value = fp.formatDate(selectedDates[0], 'Y-m-d');
          dateTo.value = fp.formatDate(selectedDates[1], 'Y-m-d');
        }
      }
    });

    function setPresetRange(val) {
      const now = new Date();
      const pad = (n) => String(n).padStart(2, '0');
      const fmt = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

      if (val === 'this_month') {
        const start = new Date(now.getFullYear(), now.getMonth(), 1);
        const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        fp.setDate([start, end], true);
        dateFrom.value = fmt(start);
        dateTo.value = fmt(end);
      }

      if (val === 'last_30') {
        const end = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const start = new Date(end);
        start.setDate(start.getDate() - 29);
        fp.setDate([start, end], true);
        dateFrom.value = fmt(start);
        dateTo.value = fmt(end);
      }
    }

    preset.addEventListener('change', (e) => setPresetRange(e.target.value));

    btnApply.addEventListener('click', () => {
      const params = new URLSearchParams(window.location.search);
      if (dateFrom.value) params.set('from', dateFrom.value); else params.delete('from');
      if (dateTo.value) params.set('to', dateTo.value); else params.delete('to');

      const baseUrl = window.location.pathname;
      window.location.href = `${baseUrl}?${params.toString()}`;
    });
  </script>
@endsection
