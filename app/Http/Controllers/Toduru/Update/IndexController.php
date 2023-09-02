<?php

namespace App\Http\Controllers\Toduru\Update;

use App\Http\Controllers\Controller;
use App\Models\Toduru;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $toduruId = (int) $request->route('toduruId');
        $toduru = Toduru::where('id', $toduruId)->firstOrFail();
        return view('toduru.update',[
            'toduru'=>$toduru,
        ]);
    }
}
