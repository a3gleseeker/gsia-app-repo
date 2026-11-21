<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){

        return view('adminside/adminindex');
    }

    public function employee_index(){
        return view('adminside/employeeindex');
    }
}
