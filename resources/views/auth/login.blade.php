@extends('layouts.master-auth')

@section('title', 'Login')

@section('content')

@include('layouts/loader')
<div class="auth-main">
    <div class="auth-wrapper v1">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="m-header justify-center" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
                            <img src="https://www.smkitbaitulaziz.sch.id/images/logo.png" style="height: 50px; width: auto;" alt="" />
                            <h5 style="margin-top:8px;">SMK IT Baitul Azis</h5>
                        </div>
                        <!-- <div class="text-center">
                                <a href="#"><img src="/build/images/logo-dark.svg" alt="img" /></a>
                            </div> -->
                        <h4 class="text-center f-w-500 my-3">Masuk dengan kredensial anda</h4>
                        <div class="mb-3">
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                value="admin" id="floatingInput" name="username"
                                placeholder="NIS / NIP / Username / Kredensial Unik" />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="floatingInput1" placeholder="Password" name="password" required
                                autocomplete="current-password" value="admin12345" />
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection