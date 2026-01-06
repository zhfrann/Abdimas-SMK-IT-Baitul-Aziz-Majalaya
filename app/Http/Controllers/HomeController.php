<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('index');
    }

    public function pageView($routeName, $page = null)
    {
        // Construct the view name based on the provided routeName and optional page parameter
        $viewName = ($page) ? $routeName.'.'.$page : $routeName;
        // Check if the constructed view exists
        if (\View::exists($viewName)) {
            // If the view exists, return the view
            return view($viewName);
        } else {
            // If the view doesn't exist, return a 404 error
            abort(404);
        }
    }

}
