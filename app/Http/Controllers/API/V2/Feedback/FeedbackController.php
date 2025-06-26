<?php

namespace App\Http\Controllers\API\V2\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * List Feedback
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable'],
            'page' => ['nullable'],
            'limit' => ['nullable'],
        ]);


        $feedback = Feedback::where('title', 'LIKE', "%$request->search%")->latest()->paginate($request->limit ?? 10);
        $data = [];

        foreach ($feedback as $item) {
            $data[] = [
                'id' => $item->id,
                'title' => $item->title,
                'feedback' => $item->feedback,
                'comment' => $item->comment,
                'created_at' => $item->created_at->format('d F Y'),
            ];
        }

        return response()->json([
            "status" => "success",
            "message" => "Data feedback berhasil didapatkan.",
            "data" => $data
        ], 200);
    }

    /**
     * Create Feedback
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'feedback' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                400
            );
        }

        Feedback::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'feedback' => $request->feedback,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Feedback anda berhasil dikirimkan, terimakasih telah membantu memberikan masukan untuk pengembangan Arisan Digital. Kami akan menindak lanjut feedback anda segera.",
        ], 200);
    }
}
