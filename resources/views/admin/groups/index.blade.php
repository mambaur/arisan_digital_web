@extends('layouts.master')

@section('title')
    Groups
@endsection

@section('page-title')
    Groups
@endsection

@section('css')
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-md-6">
            {{ Breadcrumbs::render('groups') }}
        </div>

        <div class="col-md-6">
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                <div>
                    <a href="{{route('group_create')}}" class="btn btn-primary"><i
                            class="bx bx-plus me-1"></i> Add New</a>
                </div>
            </div>
        </div>
    </div>

    <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div id="wrapper"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- gridjs js -->
    <script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>

    <script>
        new gridjs.Grid({
            columns: [
                'Code', 'Nama', 'Periods Type', {
                    name: 'Periods Date',
                    formatter: (_, row) => gridjs.html(formatDate(row.cells[3].data))
                }, {
                    name: 'Notes',
                    formatter: (_, row) => gridjs.html(
                        `<span title="${row.cells[4].data}">${truncateText(row.cells[4].data, 20)}</span>`)
                }, 'Status', {
                    name: 'Created',
                    formatter: (_, row) => gridjs.html(formatDate(row.cells[6].data))
                }, {
                    name: 'Actions',
                    sort: false,
                    formatter: (_, row) => gridjs.html(`
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item dropdown">
                            <a class="text-muted dropdown-toggle font-size-18 px-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </a>
                        
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="/group/edit/${row.cells[7].data}">Edit</a>
                                <a class="dropdown-item" href="#">Members</a>
                            </div>
                        </li>
                    </ul>
                `)
                },
            ],
            sort: {
                multiColumn: false,
                server: {
                    url: (prev, columns) => {
                        if (!columns.length) {
                            // Default sort
                            return `${prev}order[]=groups.created_at&dir[]=desc&`;
                        };

                        const col = columns[0];
                        const dir = col.direction === 1 ? 'asc' : 'desc';
                        let colName = ['groups.code', 'groups.name', 'groups.periods_type',
                                'groups.periods_date', 'groups.notes', 'groups.created_at', 'groups.status', 'groups.id'
                            ]
                            [
                                col.index
                            ];

                        return `${prev}order[]=${colName}&dir[]=${dir}&`;
                    }
                }
            },
            server: {
                url: '/groups/data?',
                then: result => result.rows.data.map(item => [item.code, item.name, item.periods_type, item
                    .periods_date, item.notes, item.status, item
                    .created_at, item.id
                ]),
                // then: data => console.log(data),
                handle: (res) => {
                    if (res.status === 404) return {
                        data: []
                    };
                    if (res.ok) return res.json();

                    throw Error('something went wrong :(');
                },
                total: result => result.rows.total
            },
            search: {
                server: {
                    url: (prev, keyword) => `${prev}search=${keyword}&`
                },
            },
            pagination: {
                limit: 15,
                server: {
                    url: (prev, page, limit) => `${prev}limit=${limit}&page=${page+1}&`
                }
            },
        }).render(document.getElementById("wrapper"));


        function truncateText(text, maxLength) {
            if (!text) {
                return '';
            }

            if (text.length <= maxLength) {
                return text;
            }
            return text.substring(0, maxLength) + '...';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            };
            return new Intl.DateTimeFormat('en-GB', options).format(date);
        }
    </script>
@endsection
