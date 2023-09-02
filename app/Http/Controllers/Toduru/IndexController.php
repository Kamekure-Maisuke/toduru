<?php

namespace App\Http\Controllers\Toduru;

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
        $todurus = Toduru::orderBY('created_at', 'DESC')->get();
        return view('toduru.index',[
            'todurus'=>$todurus,
        ]);
    }
}
