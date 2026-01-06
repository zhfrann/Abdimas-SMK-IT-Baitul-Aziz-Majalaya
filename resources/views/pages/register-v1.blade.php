@extends('layouts.master-auth')

@section('title', 'Register')

@section('content')

    @include('layouts/loader')
    <div class="auth-main">
      <div class="auth-wrapper v1">
        <div class="auth-form">
          <div class="card my-5">
            <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
              @csrf
              <div class="text-center">
                <a href="#"><img src="/build/images/logo-dark.svg" alt="img" /></a>
                <div class="d-grid my-3">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="/build/images/authentication/facebook.svg" alt="img" /> <span> Sign Up with Facebook</span>
                  </button>
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="/build/images/authentication/twitter.svg" alt="img" /> <span> Sign Up with Twitter</span>
                  </button>
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="/build/images/authentication/google.svg" alt="img" /> <span> Sign Up with Google</span>
                  </button>
                </div>
              </div>
              <div class="saprator my-3">
                <span>OR</span>
              </div>
              <h4 class="text-center f-w-500 mb-3">Sign up with your work email.</h4>
              <div class="row">
                <div class="col-sm-12">
                  <div class="mb-3">
                    <input type="text" class="form-control@error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              <div class="mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              <div class="mb-3">
                <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" required autocomplete="new-password">/>
              </div>
              <div class="d-flex mt-1 justify-content-between">
                <div class="form-check">
                  <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="" />
                  <label class="form-check-label text-muted" for="customCheckc1">I agree to all the Terms & Condition</label>
                </div>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Sign up</button>
              </div>
              <div class="d-flex justify-content-between align-items-end mt-4">
                <h6 class="f-w-500 mb-0">Already have an Account?</h6>
                <a href="{{ route('login') }}" class="link-primary">Login here</a>
              </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection
