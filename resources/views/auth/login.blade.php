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
              <div class="text-center">
                <a href="#"><img src="/build/images/logo-dark.svg" alt="img" /></a>
                <div class="d-grid my-3">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="/build/images/authentication/facebook.svg" alt="img" /> <span> Sign In with Facebook</span>
                  </button>
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="/build/images/authentication/twitter.svg" alt="img" /> <span> Sign In with Twitter</span>
                  </button>
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="/build/images/authentication/google.svg" alt="img" /> <span> Sign In with Google</span>
                  </button>
                </div>
              </div>
              <div class="saprator my-3">
                <span>OR</span>
              </div>
              <h4 class="text-center f-w-500 mb-3">Login with your email</h4>
              <div class="mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" value="admin@phoenixcoded.com" id="floatingInput" name="email" placeholder="Email Address" />
                @error('email')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
              </div>
              <div class="mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="floatingInput1" placeholder="Password" name="password" required autocomplete="current-password" value="12345678"/>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                  <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" {{ old('remember') ? 'checked' : '' }} />
                  <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                </div>
                <h6 class="text-secondary f-w-400 mb-0">
                  @if (Route::has('password.request'))
                      <a href="{{ route('password.request') }}"> Forgot Password? </a>
                  @endif
                  
                </h6>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
              <div class="d-flex justify-content-between align-items-end mt-4">
                <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
                <a href="{{ route('register') }}" class="link-primary">Create Account</a>
              </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection