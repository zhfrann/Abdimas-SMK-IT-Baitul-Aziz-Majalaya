@extends('layouts.master-auth')

@section('title', 'Check mail')

@section('content')

@include('layouts/loader')

    <div class="auth-main">
      <div class="auth-wrapper v1">
        <div class="auth-form">
          <div class="card my-5">
            <div class="card-body">
              <a href="#"><img src="/build/images/logo-dark.svg" class="mb-4 img-fluid" alt="img" /></a>
              <div class="mb-4">
                <h3 class="mb-2"><b>Hi, Check Your Mail</b></h3>
                <p class="text-muted">We have sent a password recover instructions to your email.</p>
              </div>
              <div class="d-grid mt-3">
                <button type="button" class="btn btn-primary">Sign in</button>
              </div>
              <div class="saprator mt-3">
                <span>Sign up with</span>
              </div>
              <div class="row g-2">
                <div class="col-4">
                  <div class="d-grid">
                    <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                      <img src="/build/images/authentication/google.svg" alt="img" />
                      <span class="d-none d-sm-inline-block"> Google</span>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                      <img src="/build/images/authentication/twitter.svg" alt="img" />
                      <span class="d-none d-sm-inline-block"> Twitter</span>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                      <img src="/build/images/authentication/facebook.svg" alt="img" />
                      <span class="d-none d-sm-inline-block"> Facebook</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->

@endsection