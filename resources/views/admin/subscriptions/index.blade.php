@extends('layouts.master')

@section('title')
    Subscription
@endsection

@section('page-title')
    Subscription
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
                    name: 'Identifier',
                    formatter: (_, row) => gridjs.html(
                        `<span title="${row.cells[3].data}">${truncateText(row.cells[3].data, 20)}</span>`)
                },
                {
                    name: 'Name',
                    formatter: (_, row) => gridjs.html(
                        `<span class="text-warning" title="${row.cells[4].data}">${truncateText(row.cells[4].data, 20)}</span>`)
                },
                {
                    name: 'Description',
                    formatter: (_, row) => gridjs.html(
                        `<span title="${row.cells[5].data}">${truncateText(row.cells[5].data, 20)}</span>`)
                },
                {
                    name: 'Price',
                    formatter: (_, row) => gridjs.html(
                        `<span class="text-success" title="${row.cells[6].data}">
                            ${Number(row.cells[6].data).toLocaleString('en-US', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                            })}
                        </span>`)
                },
                {
                    name: 'Created',
                    formatter: (_, row) => gridjs.html(formatDate(row.cells[7].data))
                }
            ],
            sort: {
                multiColumn: false,
                server: {
                    url: (prev, columns) => {
                        if (!columns.length) {
                            // Default sort
                            return `${prev}order[]=subscriptions.created_at&dir[]=desc&`;
                        };

                        const col = columns[0];
                        const dir = col.direction === 1 ? 'asc' : 'desc';
                        let colName = [
                            'subscriptions.id', 'users.name', 'users.email', 'subscriptions.identifier', 'subscriptions.name', 'subscriptions.description', 'subscriptions.price', 'subscriptions.created_at', null
                        ][col.index];

                        return `${prev}order[]=${colName}&dir[]=${dir}&`;
                    }
                }
            },
            server: {
                url: '/subscriptions/data?',
                then: result => result.rows.data.map(item => [item.id, item.user.name, item.user.email, item.identifier, item.name, item.description, item.price, item
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
