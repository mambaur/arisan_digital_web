@extends('layouts.master-without-nav')
@section('title')
    Reset Password Berhasil
@endsection
@section('page-title')
    Reset Password Berhasil
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <div class="authentication-bg min-vh-100">
            <div class="bg-overlay bg-light"></div>
            <div class="container">
                <div class="d-flex flex-column min-vh-100 px-3 pt-4">
                    <div class="row justify-content-center my-auto">
                        <div class="col-md-8 col-lg-6 col-xl-5">

                            <div class="mb-4 pb-2">
                                <a href="/" class="d-block auth-logo">
                                    <img src="{{ URL::asset('assets/images/logo-dark.png') }}" alt="" height="30"
                                        class="auth-logo-dark me-start">
                                    <img src="{{ URL::asset('assets/images/logo-light.png') }}" alt=""
                                        height="30" class="auth-logo-light me-start">
                                </a>
                            </div>

                            <div class="card">
                                <div class="card-body p-4">
                                    <div class="text-center mt-2">
                                        <h5>Reset Password Berhasil <i class="bx bx-check-circle text-success icon nav-icon"></i></h5>
                                        <p class="text-muted">Password anda berhasil di reset, anda bisa login menggunakan password baru anda.</p>
                                    </div>

                                </div>
                            </div>

                        </div><!-- end col -->
                    </div><!-- end row -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center p-4">
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> arisan digital. Crafted with <i
                                        class="mdi mdi-heart text-danger"></i> by Caraguna
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- end container -->
        </div>
        <!-- end authentication section -->
    @endsection
