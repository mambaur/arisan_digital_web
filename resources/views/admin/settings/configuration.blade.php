@extends('layouts.master')

@section('title')
Configuration
@endsection

@section('page-title')
Configuration
@endsection

@section('content')
    <div class="row">
        {{ Breadcrumbs::render('profile') }}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile_update', $user->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <label for="formrow-firstname-input" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                placeholder="Name" value="{{ old('name') ?? @$user->name }}" id="formrow-firstname-input">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="formrow-firstname-input" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" readonly
                                placeholder="Email" value="{{ old('email') ?? @$user->email }}">
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-md">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
@endsection
