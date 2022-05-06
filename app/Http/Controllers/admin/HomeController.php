<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{

    /*
     * Display view home dashboard
     */
    public function index()
    {
        abort_if(Gate::denies('admin.dashboard'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view("admin.dashboard.index");
    }
}
