<?php

namespace App\Http\Controllers\Admin\Member\DataGrid;

use App\Models\Member;
use WdevRs\LaravelDatagrid\DataGrid\DataGrid;
use WdevRs\LaravelDatagrid\LaravelDatagrid;

class MemberDataGrid extends DataGrid
{

    /**
     * MemberDataGrid constructor.
     */
    public function __construct()
    {
        $this->fromQuery(Member::with(['group'])->select('members.*')->join('groups', 'groups.id', 'members.group_id'))
            ->column('members.id', 'ID')
            ->column('members.email', 'email')
            ->column('groups.name', 'groups.name')
            ->column('members.no_telp', 'no_telp')
            ->column('members.no_whatsapp', 'no_whatsapp')
            ->column('members.created_at', 'created_at')
            ->column('members.name', 'name');
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
