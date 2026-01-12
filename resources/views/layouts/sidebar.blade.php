<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/dashboard/index" class="b-brand text-primary d-flex align align-items-center gap-2">
                <img src="https://www.smkitbaitulaziz.sch.id/images/logo.png" style="height: 50px; width: auto;"
                    alt="" />
                <h5 class="mt-2"> SMK IT Baitul Azis</h5>
            </a>
        </div>

        <!-- @php
            // ===== HARDCODE ROLE (sementara) =====
            $role = 'guru_mapel'; // ganti: 'walikelas' / 'super_admin' / 'guru_mapel'

            $roleLabel = match ($role) {
                'guru_mapel' => 'Guru Mapel',
                'walikelas' => 'Wali Kelas',
                'super_admin' => 'Super Admin',
                default => 'User',
            };
        @endphp -->

        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="/build/images/user/avatar-1.jpg" alt="user-image"
                                class="user-avtar wid-45 rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">Jimmy Morris</h6>
                            <small>{{ $roleLabel }}</small>
                        </div>
                        <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse"
                            href="#pc_sidebar_userlink">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sort-outline"></use>
                            </svg>
                        </a>
                    </div>

                    <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                        <div class="pt-3">
                            <a href="#!"><i class="ti ti-user"></i><span>My Account</span></a>
                            <a href="#!"><i class="ti ti-settings"></i><span>Settings</span></a>
                            <a href="#!"><i class="ti ti-lock"></i><span>Lock Screen</span></a>

                            {{-- Logout Start --}}
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-power"></i><span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            {{-- Logout End --}}
                        </div>
                    </div>
                </div>
            </div>

            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Navigation</label>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a href="/dashboard/index" class="pc-link">
                        <span class="pc-micon"><i class="bi bi-columns-gap"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                @role('Super Admin')
                    @include('layouts.menu-list-superadmin')
                @endrole

                {{-- ================== MENU GURU MAPEL + SUPER ADMIN ================== --}}
                <!-- @if ($role === 'guru_mapel')
                    @include('layouts.menu-list-mapel', [
                        'role' => $role,
                        'roleLabel' => $roleLabel,
                    ])
                @endif

                {{-- ================== MENU WALI KELAS + SUPER ADMIN ================== --}}
                @if ($role === 'walikelas' || $role === 'super_admin')
                    @include('layouts.menu-list-walas', [
                        'role' => $role,
                        'roleLabel' => $roleLabel,
                    ])
                @endif

                {{-- ================== MENU SUPER ADMIN ONLY ================== --}}
                @if ($role === 'super_admin')
                    @include('layouts.menu-list-admin', [
                        'role' => $role,
                        'roleLabel' => $roleLabel,
                    ])
                @endif -->
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
