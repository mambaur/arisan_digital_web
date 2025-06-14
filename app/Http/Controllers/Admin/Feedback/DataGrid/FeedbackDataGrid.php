<?php

namespace App\Http\Controllers\Admin\Feedback\DataGrid;

use App\Models\Feedback;
use WdevRs\LaravelDatagrid\DataGrid\DataGrid;

class FeedbackDataGrid extends DataGrid
{

    /**
     * MemberDataGrid constructor.
     */
    public function __construct()
    {
        $this->fromQuery(Feedback::with(['user'])->select('feedback.*')->join('users', 'users.id', 'feedback.user_id'))
            ->column('feedback.id', 'id')
            ->column('users.name', 'users.name')
            ->column('feedback.title', 'title')
            ->column('feedback.feedback', 'feedback')
            ->column('feedback.file_url', 'file_url')
            ->column('feedback.created_at', 'created_at')
            ->column('feedback.comment', 'comment');
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
