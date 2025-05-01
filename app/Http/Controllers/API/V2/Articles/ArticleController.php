<?php

namespace App\Http\Controllers\API\V2\Articles;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * List Articles
     */
    public function index(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'page' => 'nullable',
            'limit' => 'nullable',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $articles = Article::latest()->paginate($request->limit ?? 10);

        $data = [];

        foreach ($articles as $item) {
            $data = [
                [
                    "id" => $item->id,
                    "title" => $item->title,
                    "subtitle" => $item->subtitle,
                    "thumbnail" => $item->thumbnail,
                    "url" => $item->url,
                    "created_at" => $item->created_at->format('d F Y h:i'),
                ]
            ];
        }

        return response()->json([
            "message" => "Data artikel behasil didapatkan",
            "data" => $data,
        ]);
    }
}
