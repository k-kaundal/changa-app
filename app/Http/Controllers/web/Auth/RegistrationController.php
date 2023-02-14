<?php

namespace App\Http\Controllers\web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    function index(){
        return view('auth.registration');
    }
}
