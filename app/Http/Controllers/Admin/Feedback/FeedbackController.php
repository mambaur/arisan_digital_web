<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Admin\Feedback\DataGrid\FeedbackDataGrid;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.feedback.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function data(FeedbackDataGrid $grid, Request $request)
    {
        return $grid->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateComment(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'comment' => 'required'
        ]);

        $feedback = Feedback::find($request->id);

        if(!@$feedback){
            return abort(404);
        }

        $feedback->comment = $request->comment;
        $feedback->save();

        session()->flash('success', 'Your comment successfully updated');
        return redirect()->route('feedback');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feedback = Feedback::find($id);

        if(!@$feedback){
            return abort(404);
        }

        $feedback->delete();
        
        session()->flash('success', 'Your feedback successfully deleted');
        return redirect()->route('feedback');
    }
}
