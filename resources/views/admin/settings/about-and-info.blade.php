@extends('layouts.master')

@section('title')
    About & Info
@endsection

@section('page-title')
    About & Info
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('setting_about_info_store')}}" method="POST">
                        @csrf
                        @foreach ($data as $index => $item)
                            @if (@$item['key'] == App\Constants\SettingType::IS_MAINTENANCE)    
                                <div class="mb-3 row">
                                    <label for="example-text-input" class="col-md-2 col-form-label">{{@$item['title']}}</label>
                                    <div class="col-md-10">
                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                            <input type="hidden" name="keys[{{$index}}]" value="{{@$item['key']}}">
                                            <input type="checkbox" class="form-check-input" name="values[{{$index}}]" value="1" @if(@$item['value'] == '1') checked @endif>
                                            <label class="form-check-label" for="customSwitchsizelg">Ya</label>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-3 row">
                                    <label for="example-text-input" class="col-md-2 col-form-label">{{@$item['title']}}</label>
                                    <div class="col-md-10">
                                        <input type="hidden" name="keys[{{$index}}]" value="{{@$item['key']}}">
                                        <input class="form-control" type="text" name="values[{{$index}}]" value="{{@$item['value']}}" id="example-text-input">
                                    </div>
                                </div>
                            @endif
                        @endforeach
    
                        <button type="submit" class="btn btn-primary text-end px-4">Submit</button>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- end row -->
@endsection

@section('scripts')
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
@endsection
