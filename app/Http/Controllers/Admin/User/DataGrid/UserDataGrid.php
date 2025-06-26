<?php

namespace App\Http\Controllers\Admin\User\DataGrid;

use App\Models\User;
use WdevRs\LaravelDatagrid\DataGrid\DataGrid;

class UserDataGrid extends DataGrid
{

    /**
     * MemberDataGrid constructor.
     */
    public function __construct()
    {
        $this->fromQuery(User::query())
            ->column('users.id', 'id')
            ->column('users.photo_url', 'photo_url')
            ->column('users.email', 'email')
            ->column('users.created_at', 'created_at')
            ->column('users.last_seen_at', 'last_seen_at')
            ->column('users.name', 'name');
    }

    public function render(string $view = 'laravel-datagrid::datagrid')
    {
        $request = request();

        if ($request->expectsJson()) {
            return $this->getData($request);
        }

        return [
            'baseUrl' => $request->url(),
            'columns' => $this->columns,
            'rows' => $this->getData($request)
        ];
    }

    /**
     * @param $request
     * @return array
     */
    protected function getData($request): array
    {
        $paginator = $this->search($request->search)
            ->sort($request->order, $request->dir)
            ->paginate($request->limit)
            ->withQueryString();

        return [
            'key' => $this->key,
            // 'data' => $this->format($paginator->items()),
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'currentPage' => $paginator->currentPage(),
            'search' => $request->search,
            'order' => $request->order,
            'dir' => $request->dir,
            'limit' => $paginator->perPage(),
            'paginationLinks' => $paginator->linkCollection()->toArray()
        ];
    }
}
