@extends('layouts.master')

@section('title')
    User
@endsection

@section('page-title')
    User
@endsection

@section('css')
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-md-6">
            {{ Breadcrumbs::render('users') }}
        </div>

        <div class="col-md-6">
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                <div>
                    <a href="#" data-bs-toggle="modal" data-bs-target=".add-new" class="btn btn-primary"><i
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
    <!-- end row -->


    </div>
    <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <!--  successfully modal  -->
    <div id="success-btn" class="modal fade" tabindex="-1" aria-labelledby="success-btnLabel" aria-hidden="true"
        data-bs-scroll="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="bx bx-check-circle display-1 text-success"></i>
                        <h4 class="mt-3">User Added Successfully</h4>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!--  Extra Large modal example -->
    <div class="modal fade add-new" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Add New</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="AddNew-Username">Username</label>
                                <input type="text" class="form-control" placeholder="Enter Username"
                                    id="AddNew-Username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <select class="form-select">
                                    <option selected>Select Position</option>
                                    <option>Full Stack Developer</option>
                                    <option>Frontend Developer</option>
                                    <option>UI/UX Designer</option>
                                    <option>Backend Developer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="AddNew-Email">Email</label>
                                <input type="text" class="form-control" placeholder="Enter Email" id="AddNew-Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="AddNew-Phone">Phone</label>
                                <input type="text" class="form-control" placeholder="Enter Phone" id="AddNew-Phone">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-danger me-1" data-bs-dismiss="modal"><i
                                    class="bx bx-x me-1 align-middle"></i> Cancel</button>
                            <button type="submit" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#success-btn" id="btn-save-event"><i
                                    class="bx bx-check me-1 align-middle"></i> Confirm</button>
                        </div>
                    </div>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('scripts')
    <!-- gridjs js -->
    <script src="assets/libs/gridjs/gridjs.umd.js"></script>

    <!-- App js -->
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>

    <script>
        new gridjs.Grid({
            columns: [
                // {
                //     name: 'Photo',
                //     formatter: (_, row) => gridjs.html(`<img src="${row.cells[0].data}" alt=""
                //                                 class="avatar rounded-circle img-thumbnail me-2">`)
                // },
                {
                    name: 'Photo',
                    formatter: (_, row) => gridjs.html(`<img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt=""
                                                class="avatar rounded-circle img-thumbnail me-2">`)
                },
                'Name', 'Email', {
                    name: 'Created',
                    formatter: (_, row) => gridjs.html(formatDate(row.cells[3].data))
                }, {
                    name: 'Actions',
                    sort: false,
                    formatter: (_, row) => gridjs.html(`
                <ul class="list-inline mb-0">
                                                <li class="list-inline-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Edit" class="px-2 text-primary"><i
                                                            class="bx bx-pencil font-size-18"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Delete" class="px-2 text-danger"><i
                                                            class="bx bx-trash-alt font-size-18"></i></a>
                                                </li>
                                                <li class="list-inline-item dropdown">
                                                    <a class="text-muted dropdown-toggle font-size-18 px-2" href="#"
                                                        role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </a>

                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="#">Action</a>
                                                        <a class="dropdown-item" href="#">Another action</a>
                                                        <a class="dropdown-item" href="#">Something else here</a>
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
                            return `${prev}order[]=users.created_at&dir[]=desc&`;
                        };

                        const col = columns[0];
                        const dir = col.direction === 1 ? 'asc' : 'desc';
                        let colName = ['users.photo_url', 'users.name', 'users.email', 'users.created_at', null]
                            [
                                col.index
                            ];

                        return `${prev}order[]=${colName}&dir[]=${dir}&`;
                    }
                }
            },
            server: {
                url: '/users/data?',
                then: result => result.rows.data.map(item => [item.photo_url, item.name, item.email, item
                    .created_at, null
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
