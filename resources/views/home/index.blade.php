@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('css')
    <!-- jsvectormap css -->
    <link href="{{ URL::asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('page-title')
    Dashboard
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-title rounded bg-soft-primary">
                                                <i class="bx bx-group font-size-24 mb-0 text-primary"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 font-size-15">Total Grub Baru</h6>
                                            <div class="">Sebulan terakhir</div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="mt-4 pt-1 mb-0 font-size-22">{{$total_group['last_30_days']}}
                                            @if ($total_group['percent'] >= 0)
                                                <span
                                                    class="text-success fw-medium font-size-13 align-middle"> <i
                                                        class="mdi mdi-arrow-up"></i> {{$total_group['percent']}}% </span>
                                            @else
                                                <span
                                                class="text-danger fw-medium font-size-13 align-middle"> <i
                                                    class="mdi mdi-arrow-down"></i> {{$total_group['percent']}}% </span>
                                            @endif
                                        </h4>
                                        <div class="d-flex mt-1 align-items-end overflow-hidden">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0 text-truncate">
                                                    Total bulan sebelumnya <b>{{$total_group['prev_30_start_days']}}</b>
                                                    <br>
                                                    Total hari ini <b>{{$total_group['today']}}</b>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-title rounded bg-soft-primary">
                                                <i class="bx bx-shuffle font-size-24 mb-0 text-primary"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 font-size-15">Total kocokan</h6>
                                            <div class="">Sebulan terakhir</div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="mt-4 pt-1 mb-0 font-size-22">{{$total_shuffle['last_30_days']}}
                                            @if ($total_shuffle['percent'] >= 0)
                                                <span
                                                    class="text-success fw-medium font-size-13 align-middle"> <i
                                                        class="mdi mdi-arrow-up"></i> {{$total_shuffle['percent']}}% </span>
                                            @else
                                                <span
                                                class="text-danger fw-medium font-size-13 align-middle"> <i
                                                    class="mdi mdi-arrow-down"></i> {{$total_shuffle['percent']}}% </span>
                                            @endif
                                        </h4>
                                        <div class="d-flex mt-1 align-items-end overflow-hidden">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0 text-truncate">Total bulan sebelumnya <b>{{$total_shuffle['prev_30_start_days']}}</b>
                                                    <br>
                                                    Total hari ini <b>{{$total_shuffle['today']}}</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-title rounded bg-soft-primary">
                                                <i class="bx bx-ghost font-size-24 mb-0 text-primary"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 font-size-15">Total Pengguna Baru</h6>
                                            <div class="">Sebulan terakhir</div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="mt-4 pt-1 mb-0 font-size-22">{{$total_user['last_30_days']}}
                                            @if ($total_user['percent'] >= 0)
                                                <span
                                                    class="text-success fw-medium font-size-13 align-middle"> <i
                                                        class="mdi mdi-arrow-up"></i> {{$total_user['percent']}}% </span>
                                            @else
                                                <span
                                                class="text-danger fw-medium font-size-13 align-middle"> <i
                                                    class="mdi mdi-arrow-down"></i> {{$total_user['percent']}}% </span>
                                            @endif
                                        </h4>
                                        <div class="d-flex mt-1 align-items-end overflow-hidden">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0 text-truncate">Total bulan sebelumnya <b>{{$total_user['prev_30_start_days']}}</b>
                                                    <br>
                                                    Total hari ini <b>{{$total_user['today']}}</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-title rounded bg-soft-primary">
                                                <i class="bx bx-gift font-size-24 mb-0 text-primary"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 font-size-15">Total Subscription</h6>
                                            <div class="">Sebulan Terakhir</div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="mt-4 pt-1 mb-0 font-size-22">{{$total_subscription['last_30_days']}}
                                            @if ($total_subscription['percent'] >= 0)
                                                <span
                                                    class="text-success fw-medium font-size-13 align-middle"> <i
                                                        class="mdi mdi-arrow-up"></i> {{$total_subscription['percent']}}% </span>
                                            @else
                                                <span
                                                class="text-danger fw-medium font-size-13 align-middle"> <i
                                                    class="mdi mdi-arrow-down"></i> {{$total_subscription['percent']}}% </span>
                                            @endif
                                        </h4>
                                        <div class="d-flex mt-1 align-items-end overflow-hidden">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0 text-truncate">Total bulan sebelumnya <b>{{$total_subscription['prev_30_start_days']}}</b>
                                                    <br>
                                                    Total hari ini <b>{{$total_subscription['today']}}</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-title rounded bg-soft-primary">
                                                <i class="bx bx-message-dots font-size-24 mb-0 text-primary"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 font-size-15">Total Feedback</h6>
                                            <div class="">Sebulan Terakhir</div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="mt-4 pt-1 mb-0 font-size-22">{{$total_feedback['last_30_days']}}
                                            @if ($total_feedback['percent'] >= 0)
                                                <span
                                                    class="text-success fw-medium font-size-13 align-middle"> <i
                                                        class="mdi mdi-arrow-up"></i> {{$total_feedback['percent']}}% </span>
                                            @else
                                                <span
                                                class="text-danger fw-medium font-size-13 align-middle"> <i
                                                    class="mdi mdi-arrow-down"></i> {{$total_feedback['percent']}}% </span>
                                            @endif
                                        </h4>
                                        <div class="d-flex mt-1 align-items-end overflow-hidden">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0 text-truncate">Total bulan sebelumnya <b>{{$total_feedback['prev_30_start_days']}}</b>
                                                    <br>
                                                    Total hari ini <b>{{$total_feedback['today']}}</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-title rounded bg-soft-primary">
                                                <i class="bx bx-show font-size-24 mb-0 text-primary"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 font-size-15">Total Last Seen</h6>
                                            <div class="">Sebulan Terakhir</div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="mt-4 pt-1 mb-0 font-size-22">{{$total_last_seen['last_30_days']}}
                                        </h4>
                                        <div class="d-flex mt-1 align-items-end overflow-hidden">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0 text-truncate">Total hari ini <b>{{$total_last_seen['today']}}</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    @endsection
    @section('scripts')
        <script src="{{ URL::asset('assets/js/pages/dashboard.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    @endsection
