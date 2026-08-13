<?php

namespace App\Controllers;

use App\Dows\CompanyDow;

class CompanyController extends BaseController
{

    private $dow;

    public function __construct()
    {
        $this->dow = new CompanyDow();
    }

    public function index($request)
    {
        return Response::json($this->dow->index($request));
    }

    public function update($request)
    {
        return Response::json($this->dow->update($request));
    }
}
