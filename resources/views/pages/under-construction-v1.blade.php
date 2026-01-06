@extends('layouts.master-auth')

@section('title', 'Site is Under Maintenance')

@section('content')

    @include('layouts/loader')
    <!-- [ Main Content ] start -->
    <div class="maintenance-block construction-card-1">
      <div class="container">
        <div class="row">
          <!-- [ sample-page ] start -->
          <div class="col-sm-12">
            <div class="card construction-card">
              <div class="card-body">
                <div class="construction-image-block">
                  <div class="row justify-content-center align-items-center construction-card-bottom">
                    <div class="col-md-6">
                      <div class="text-center">
                        <h1 class="mt-4"><b>Under Construction</b></h1>
                        <p class="mt-4 text-muted"
                          >Hey! Please check out this site later. We are doing <br />
                          some maintenance on it right now.</p
                        >
                        <a href="/dashboard/index" class="btn btn-primary mb-3">Back To Home</a>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <img class="img-fluid" src="/build/images/pages/img-cunstruct-1.svg" alt="img" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ sample-page ] end -->
      </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection