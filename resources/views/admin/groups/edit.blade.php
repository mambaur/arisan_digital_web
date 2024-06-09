@extends('layouts.master')
@section('title')
    {{ $group->name }}
@endsection

@section('page-title')
    {{ $group->name }}
@endsection

@section('css')
    <!-- choices css -->
    <link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/choices/app-choices.css') }}" rel="stylesheet" type="text/css" />

    <!-- datepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    <div class="row d-flex justify-content-center">
        {{ Breadcrumbs::render('group/edit', $group) }}
        <div class="col-lg-6 col-12">
            <div class="card py-2">
                <div class="card-body">
                    <form action="{{ route('group_update', $group->id) }}" method="post">
                        @method('put')
                        @csrf
                        
                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-3 col-form-label">User Owner</label>
                            <div class="col-md-9">
                                <select class="form-control @error('user_id') is-invalid @enderror" name="user_id"
                                    data-trigger name="choices-single-default" id="user-text-input"
                                    placeholder="Search User">
                                    <option value="">Search User</option>
                                    @if (@$group->user)
                                        
                                    <option value="{{@$group->user->id}}" selected>{{@$group->user->name." ({$group->user->email})"}}</option>
                                    @endif
                                </select>

                                @error('user_id')
                                    <div class="text-danger">The user field is required.</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label">Group Name</label>
                            <div class="col-md-9">
                                <input class="form-control @error('name') is-invalid @enderror" type="text"
                                    name="name"
                                    value="{{ old('name') ?? @$group->name }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label">Periods</label>
                            <div class="col-md-9">
                                <select class="form-select @error('periods_type') is-invalid @enderror" name="periods_type">
                                    <option value="monthly" @if((old('periods_type') ?? @$group->periods_type) == 'monthly') selected @endif>Monthly</option>
                                    <option value="weekly" @if((old('periods_type') ?? @$group->periods_type) == 'weekly') selected @endif>Weekly</option>
                                </select>
                                @error('periods_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label">Dues</label>
                            <div class="col-md-9">
                                <input class="form-control @error('dues') is-invalid @enderror" type="text"
                                    name="dues" id="dues"
                                    value="{{ old('dues') ?? @$group->dues }}">
                                @error('dues')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label">Target</label>
                            <div class="col-md-9">
                                <input class="form-control @error('target') is-invalid @enderror" type="text"
                                    name="target" id="target"
                                    value="{{ old('target') ?? @$group->target }}">
                                @error('target')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label">Period Date</label>
                            <div class="col-md-9">
                                <input type="text" name="periods_date"
                                    class="form-control flatpickr-input @error('periods_date') is-invalid @enderror"
                                    id="datepicker-humanfd" value="{{ old('periods_date') ?? @$group->periods_date }}">
                                @error('periods_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label">Notes</label>
                            <div class="col-md-9">
                                <textarea class="form-control" type="text" rows="5" name="notes">{{ old('notes') ?? @$group->notes }}</textarea>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-search-input" class="col-md-3 col-form-label"></label>
                            <div class="col-md-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status"
                                        id="status-group" value="1"
                                        @if ((old('status') ?? @$group->status) == 'active') checked @endif>
                                    <label class="form-check-label" for="status-group">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
                        </div>
                    </form>
                    <div>
                        <form action="{{route('group_delete', $group->id)}}" method="post">
                            @csrf
                            @method('delete')

                            <button type="submit" onclick="return confirm('Are you sure want to delete {{$group->name}}?')" class="btn btn-outline-danger w-100 mt-2">Delete</button>
                        </form>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('scripts')
    <!-- choices js -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    
    <!-- datepicker js -->
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <!-- form mask -->
    <script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        const element = document.getElementById('user-text-input');

        const choices = new Choices(element, {
            searchEnabled: true,
            searchChoices: false,
            placeholder: true,
            placeholderValue: 'Type to search',
            noResultsText: 'No results found',
        });

        let searchTimeout;

        element.addEventListener('search', function(event) {
            const searchTerm = event.detail.value;

            if (searchTimeout) clearTimeout(searchTimeout);

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `/users/search/data?search=${searchTerm}`
                    );
                    const data = await response.json();

                    console.log(data)

                    const items = data.map(item => ({
                        value: item.id,
                        label: item.value,
                    }));

                    choices.clearChoices();
                    choices.setChoices(items, 'value', 'label', true);
                } catch (error) {
                    console.error('Error fetching data:', error);
                }
            }, 300);
        });

        flatpickr("#datepicker-humanfd", {
            altInput: !0,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d"
        });

        setImask(document.getElementById('dues'));
        setImask(document.getElementById('target'));

        function setImask(element) {
            IMask(element, {
                mask: Number,
                scale: 2,
                thousandsSeparator: ',',
                padFractionalZeros: false,
                normalizeZeros: true,
                radix: ',',
                mapToRadix: ['.'],
                autofix: true,
            })
        }
    </script>
@endsection
