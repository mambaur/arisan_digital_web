<?php

namespace App\Http\Controllers\Admin\Group\DataGrid;

use App\Models\Group;
use WdevRs\LaravelDatagrid\DataGrid\DataGrid;

class GroupDataGrid extends DataGrid
{

    /**
     * MemberDataGrid constructor.
     */
    public function __construct()
    {
        $this->fromQuery(Group::query())
            ->column('groups.id', 'id')
            ->column('groups.code', 'code')
            ->column('groups.periods_type', 'periods_type')
            ->column('groups.periods_date', 'periods_date')
            ->column('groups.notes', 'notes')
            ->column('groups.status', 'status')
            ->column('groups.created_at', 'created_at')
            ->column('groups.name', 'name');
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
