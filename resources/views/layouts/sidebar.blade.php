<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/dashboard/index" class="b-brand text-primary d-flex align align-items-center gap-2">
                {{-- <img src="https://www.smkitbaitulaziz.sch.id/images/logo.png" style="height: 50px; width: auto;"
                    alt="" /> --}}
                <img src="{{ asset('build/images/logo.png') }}" style="height: 50px; width: auto;" alt="" />
                <h5 class="mt-2"> SMK IT Baitul Azis</h5>
            </a>
        </div>

        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="/build/images/user/avatar-1.jpg" alt="user-image"
                                class="user-avtar wid-45 rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                            <small>{{ auth()->user()->roles->first()->name }}</small>
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


                @role('Super Admin')
                    @include('layouts.menu-list-superadmin')
                @endrole

                @role('Guru Mapel')
                    @include('layouts.menu-list-mapel')
                @endrole

                @role('Wali Kelas')
                    @include('layouts.menu-list-walas')
                @endrole

                @role('Bagian Akademik')
                @include('layouts.menu-list-akademik')
                @endrole


            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->