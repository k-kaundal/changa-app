<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{


    function __construct(){
        $this->middleware('guest')->except('logout');
    }
    
    function index(){
        return view('layouts.dashboard');
    }
    function users(){
        try{
            $users = User::where('user_type','2')->get();
            return view('layouts.users')->with('users',$users);
            }catch(\Throwable $e){
            return  view('layouts.users');
            }
       
    }

    function edit_user(){
        return view('layouts.users.edit');
    }

    function view_user(){
       
        return view('layouts.users.view');
    }
}
