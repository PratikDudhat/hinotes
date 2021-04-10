<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;
use App\User;
use App\Challange;
use App\Category;


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
		$user = User::where('user_type','user')->count();
		$tag = Tag::count();
		$category = Category::count();
		$challenge = Challange::count();
        return view('home',compact('user','tag','category','challenge'));
    }
}
