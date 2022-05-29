<?php

namespace App\Http\Controllers\traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

trait PermissionGateTraits{

    /**
     * Handle permission route
     * @param $route
     */
    public function gateDeny($route){
        abort_if(
            Gate::denies($route),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
    }

}
