<?php

namespace App\Http\Controllers\web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
class LoginController extends Controller
{
    function index(){
        return view('auth.login');
    }


    function logout(){
        Session::flush();
        Auth::logout();
        return redirect('login');
    }
}
