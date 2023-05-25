<?php

namespace App\Controllers\report;


use App\Controllers\baseController;

class rroom extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\report\rroom_m();
        $data = $data->data();
        return view('report/rroom_v', $data);
    }
}
