<?php

namespace App\Http\Controllers\Toduru\Update;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toduru\UpdateRequest;
use App\Models\Toduru;

class PutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateRequest $request)
    {
        $toduru = Toduru::where('id', $request->id())->firstOrFail();
        $toduru->content = $request->toduru();
        $toduru->save();
        return redirect()
            ->route('toduru.update.index',['toduruId' => $toduru->id])
            ->with('feedback.success', "編集しました。"); // セッションデータ
    }
}
