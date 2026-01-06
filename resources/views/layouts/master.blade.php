<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>@yield('title') | Able Pro Dashboard Template</title>
    @include('layouts/head-page-meta')

    @yield('css')

    @include('layouts/head-css')
</head>
<!-- [Head] end -->
<!-- [Body] Start -->
<body data-pc-preset="{{config('app.preset_theme')}}" data-pc-sidebar-caption="{{config('app.caption_show')}}" data-pc-layout="{{config('app.theme_layout')}}" data-pc-direction="{{config('app.rtlflag')}}" data-pc-theme="{{config('app.dark_layout') ?  config('app.dark_layout') == 'default' ?? 'dark' : 'light'}}">

    @include('layouts/layout-vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @yield('content')
        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('layouts/footer-block')
    @include('layouts/customizer')

    @include('layouts/footer-js')

    @hasSection('scripts')
        @yield('scripts')
    @else
        <script>
            localStorage.setItem('layout', 'tab');
        </script>
    @endif
</body>
<!-- [Body] end -->

</html>