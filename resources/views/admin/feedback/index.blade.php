@extends('layouts.master')

@section('title')
    Feedback
@endsection

@section('page-title')
    Feedback
@endsection

@section('css')
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
@endsection

@section('content')

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

    <!--  Comment modal example -->
    <div class="modal fade add-new" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Add Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('feedback_update_comment')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="AddNew-Username">Comment</label>
                                    <input type="hidden" name="id" id="id">
                                    <textarea name="comment" id="comment" rows="5" class="form-control" required></textarea>
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
                    </form>
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
                {
                    name: 'ID',
                    hidden: true
                },
                {
                    name: 'User',
                    formatter: (_, row) => gridjs.html(
                        `<span title="${row.cells[1].data}">${truncateText(row.cells[1].data, 20)}</span><br><span class="text-muted text-small" title="${row.cells[2].data}">${truncateText(row.cells[2].data, 20)}</span>`)
                },
                {
                    name: 'Email',
                    hidden: true
                },
                {
                    name: 'Title',
                    formatter: (_, row) => gridjs.html(
                        `<span title="${row.cells[3].data}">${truncateText(row.cells[3].data, 20)}</span>`)
                },
                {
                    name: 'Feedback',
                    formatter: (_, row) => gridjs.html(
                        `<span class="text-warning" title="${row.cells[4].data}">${truncateText(row.cells[4].data, 20)}</span>`)
                },
                {
                    name: 'Comment',
                    formatter: (_, row) => gridjs.html(
                        `<span class="text-success" title="${row.cells[5].data}">${truncateText(row.cells[5].data, 20)}</span>`)
                },
                {
                    name: 'Created',
                    formatter: (_, row) => gridjs.html(formatDate(row.cells[6].data))
                },
                {
                    name: 'Actions',
                    sort: false,
                    formatter: (_, row) => gridjs.html(`
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Comment" class="px-2 text-primary comment-btn" data-id="${row.cells[0].data}" data-comment="${row.cells[5].data ?? ''}"><i
                                        class="bx bx-message-dots font-size-18"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="javascript:void(0);" onclick="confirmDelete(this)" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Delete" class="px-2 text-danger"><i
                                        class="bx bx-trash-alt font-size-18"></i></a>

                                <!-- Hidden form langsung setelah <a> -->
                                <form action="/feedback/delete/${row.cells[0].data}" method="POST" style="display: none;" class="delete-form">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </form>
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
                            return `${prev}order[]=feedback.created_at&dir[]=desc&`;
                        };

                        const col = columns[0];
                        const dir = col.direction === 1 ? 'asc' : 'desc';
                        let colName = [
                            'feedback.id', 'users.name', 'users.email', 'feedback.title', 'feedback.feedback', 'feedback.comment', 'feedback.created_at', null
                        ][col.index];

                        return `${prev}order[]=${colName}&dir[]=${dir}&`;
                    }
                }
            },
            server: {
                url: '/feedback/data?',
                then: result => result.rows.data.map(item => [item.id, item.user.name, item.user.email, item.title, item.feedback, item.comment, item
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

        function truncateText(text, maxLength) {
            if (!text) {
                return '';
            }

            if (text.length <= maxLength) {
                return text;
            }
            return text.substring(0, maxLength) + '...';
        }

        // Event delegation: listen pada document
        document.addEventListener("DOMContentLoaded", function () {
            document.body.addEventListener("click", function (e) {
            // Cek apakah yang diklik adalah tombol comment (class .comment-btn)
            if (e.target.closest(".comment-btn")) {
                const btn = e.target.closest(".comment-btn");

                // Ambil data dari attribute
                const id = btn.getAttribute("data-id");
                const comment = btn.getAttribute("data-comment");

                // Isi input modal dengan data
                document.getElementById("id").value = id ?? "";
                document.getElementById("comment").value = comment ?? "";

                // Tampilkan modal
                const modal = new bootstrap.Modal(document.querySelector(".add-new"));
                modal.show();
            }
            });
        });

        function confirmDelete(element) {
            if (confirm('Are you sure you want to delete this item?')) {
            // Cari form setelah <a>
            const form = element.nextElementSibling;
                if (form && form.tagName === 'FORM') {
                    form.submit();
                } else {
                    console.error('Form not found after the clicked element.');
                }
            }
        }
    </script>
@endsection
