<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{


    function __construct(){
        $this->middleware('auth')->except('logout');
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

    function edit_user($id){
        try{
            $user = User::where('id',$id)->first();
            return view('layouts.users.edit')->with('user',$user);
        } catch(\Throwable $e){
            return view('layouts.users.edit')->with('error',$e);
        }

       
    }

    function view_user(){
       
        return view('layouts.users.view');
    }
}
