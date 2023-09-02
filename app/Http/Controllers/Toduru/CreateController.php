<?php

namespace App\Http\Controllers\Toduru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toduru\CreateRequest;
use App\Models\Toduru;

class CreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateRequest $request)
    {
        $toduru = new Toduru;
        $toduru->content = $request->toduru();
        $toduru->save();
        return redirect()->route('toduru.index');
    }
}
