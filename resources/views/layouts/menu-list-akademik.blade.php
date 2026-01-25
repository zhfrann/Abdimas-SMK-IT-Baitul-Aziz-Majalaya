<li class="pc-item pc-caption">
    <label>Beranda</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('dashboard') }}" class="pc-link">
        <span class="pc-micon"><i class="bi bi-columns-gap"></i></span>
        <span class="pc-mtext">Dashboard</span>
    </a>
</li>

<li class="pc-item pc-caption">
    <label>Manajement Staff</label>
    <svg class="pc-icon">
        <use xlink:href="#custom-flag"></use>
    </svg>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('akademik.staff.index') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-status-up"></use>
      </svg> --}}
            <i class="bi bi-people"></i>
        </span>
        <span class="pc-mtext">List Staff</span></a>
</li>

<li class="pc-item pc-caption">
    <label>Manajement kelas</label>
    <svg class="pc-icon">
        <use xlink:href="#custom-flag"></use>
    </svg>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('akademik.tahun_ajaran.index') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-status-up"></use>
      </svg> --}}
            <i class="bi bi-calendar-date"></i>
        </span>
        <span class="pc-mtext">Tahun Ajaran</span></a>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('akademik.kelas.index') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-status-up"></use>
      </svg> --}}
            <i class="bi bi-mortarboard"></i>
        </span>
        <span class="pc-mtext">List kelas</span></a>
</li>
<li class="pc-item pc-caption">
    <label>Manajement Mapel</label>
    <svg class="pc-icon">
        <use xlink:href="#custom-flag"></use>
    </svg>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('intrakurikuler.index') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-status-up"></use>
      </svg> --}}
            <i class="bi bi-journal-bookmark"></i>
        </span>
        <span class="pc-mtext">Intrakurikuler</span></a>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('ekstrakurikuler.index') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-shield"></use>
      </svg> --}}
            <i class="bi bi-people"></i>
        </span>
        <span class="pc-mtext">Ekstrakurikuler</span></a>
</li>

<li class="pc-item pc-caption">
    <label>Absensi</label>
    <svg class="pc-icon">
        <use xlink:href="#custom-flag"></use>
    </svg>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('absensi.intrakurikuler.list') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-status-up"></use>
      </svg> --}}
            <i class="bi bi-journal-bookmark"></i>
        </span>
        <span class="pc-mtext">Absensi Intrakurikuler</span></a>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('ekstrakurikuler.index') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-shield"></use>
      </svg> --}}
            <i class="bi bi-people"></i>
        </span>
        <span class="pc-mtext">Absensi Ekstrakurikuler</span></a>
</li>

<li class="pc-item pc-caption">
    <label>Cetak dokument</label>
    <svg class="pc-icon">
        <use xlink:href="#custom-flag"></use>
    </svg>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('dokumen.kelas') }}" class="pc-link">
        <span class="pc-micon">
            {{-- <svg class="pc-icon">
        <use xlink:href="#custom-status-up"></use>
      </svg> --}}
            <i class="bi bi-journal-bookmark"></i>
        </span>
        <span class="pc-mtext">Rapor</span></a>
</li>
<li class="pc-item pc-hasmenu">
    <a href="{{ route('dokumen.mutasi') }}" class="pc-link">
        <span class="pc-micon">
            <i class="bi bi-arrow-left-right"></i>
        </span>
        <span class="pc-mtext">Mutasi</span>
    </a>
</li>


{{-- <li class="pc-item pc-caption">
  <label>Other</label>
  <svg class="pc-icon">
    <use xlink:href="#custom-notification-status"></use>
  </svg>
</li>
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link"
    ><span class="pc-micon">
      <svg class="pc-icon">
        <use xlink:href="#custom-level"></use>
      </svg> </span
    ><span class="pc-mtext">Menu levels</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span
  ></a>
  <ul class="pc-submenu">
    <li class="pc-item"><a class="pc-link" href="#!">Level 2.1</a></li>
    <li class="pc-item pc-hasmenu">
      <a href="#!" class="pc-link"
        >Level 2.2<span class="pc-arrow"><i data-feather="chevron-right"></i></span
      ></a>
      <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
        <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            >Level 3.3<span class="pc-arrow"><i data-feather="chevron-right"></i></span
          ></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Level 4.1</a></li>
            <li class="pc-item"><a class="pc-link" href="#!">Level 4.2</a></li>
          </ul>
        </li>
      </ul>
    </li>
    <li class="pc-item pc-hasmenu">
      <a href="#!" class="pc-link"
        >Level 2.3<span class="pc-arrow"><i data-feather="chevron-right"></i></span
      ></a>
      <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
        <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            >Level 3.3<span class="pc-arrow"><i data-feather="chevron-right"></i></span
          ></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Level 4.1</a></li>
            <li class="pc-item"><a class="pc-link" href="#!">Level 4.2</a></li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>
</li> --}}
