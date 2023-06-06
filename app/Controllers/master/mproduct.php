<?php

namespace App\Controllers\master;


use App\Controllers\baseController;

class mproduct extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\master\mproduct_m();
        $data = $data->data();
        $data["page"]="produk";
        return view('master/mproduct_v', $data);
    }


    public function room()
    {
        $data = new \App\Models\master\mproduct_m();
        $data = $data->data();
        $data["page"]="room";
        return view('master/mproduct_v', $data);
    }


    public function loker()
    {
        $data = new \App\Models\master\mproduct_m();
        $data = $data->data();
        $data["page"]="loker";
        return view('master/mproduct_v', $data);
    }

    
    public function buy()
    {
        //buy
        if ($this->request->getVar("product_id")) {
            $productd["product_id"] = $this->request->getVar("product_id");
        } else {
            $productd["product_id"] = -1;
        }
        $purchase=$this->db->table("purchased")
        ->orderBy("purchased_id ","DESC")
        ->limit(1)
        ->getWhere($productd);
        $data["product_buy"] = 0;
        foreach ($purchase->getResult() as $purchase) {$data["product_buy"] = $purchase->purchased_price/$purchase->purchased_qty;}
        echo  $data["product_buy"];
    }
}
