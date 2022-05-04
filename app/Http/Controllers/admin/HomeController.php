<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /*
     * Display view home dashboard
     */
    public function index(){
        return view("admin.dashboard.index");
    }
}
