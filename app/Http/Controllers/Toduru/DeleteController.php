<?php

namespace App\Http\Controllers\Toduru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Toduru;

class DeleteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $toduruId = (int) $request->route('toduruId');
        Toduru::destroy($toduruId);
        return redirect()
        ->route('toduru.index')
        ->with('feedback.success', "削除しました。");
    }
}
