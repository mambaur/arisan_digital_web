<?php

namespace App\Http\Controllers\Admin\Subscription\DataGrid;

use App\Models\Subscription;
use WdevRs\LaravelDatagrid\DataGrid\DataGrid;

class SubscriptionDataGrid extends DataGrid
{

    /**
     * MemberDataGrid constructor.
     */
    public function __construct()
    {
        $this->fromQuery(Subscription::with(['user'])->select('subscriptions.*')->join('users', 'users.id', 'subscriptions.user_id'))
            ->column('subscriptions.id', 'id')
            ->column('users.name', 'users.name')
            ->column('subscriptions.identifier', 'identifier')
            ->column('subscriptions.name', 'name')
            ->column('subscriptions.description', 'description')
            ->column('subscriptions.price', 'price')
            ->column('subscriptions.created_at', 'created_at');
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
