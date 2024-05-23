@extends('layouts.master')
@section('title')
    Shops
@endsection
@section('css')
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
@endsection
@section('page-title')
    Shops
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div id="table-ecommerce-shops"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    @endsection
    @section('scripts')
        <!-- gridjs js -->
        <script src="{{ URL::asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>

        <script src="{{ URL::asset('assets/js/pages/ecommerce-shops.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    @endsection
