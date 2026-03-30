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
        $data["page"] = "produk";
        return view('master/mproduct_v', $data);
    }


    public function room()
    {
        $data = new \App\Models\master\mproduct_m();
        $data = $data->data();
        $data["page"] = "room";
        return view('master/mproduct_v', $data);
    }

     public function paket()
    {
        $data = new \App\Models\master\mproduct_m();
        $data = $data->data();
        $data["page"] = "paket";
        return view('master/mproduct_v', $data);
    }


    public function loker()
    {
        $data = new \App\Models\master\mproduct_m();
        $data = $data->data();
        $data["page"] = "loker";
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
        $purchase = $this->db->table("purchased")
            ->orderBy("purchased_id ", "DESC")
            ->limit(1)
            ->getWhere($productd);
        $data["product_buy"] = 0;
        foreach ($purchase->getResult() as $purchase) {
            $data["product_buy"] = $purchase->purchased_price / $purchase->purchased_qty;
        }
        echo  $data["product_buy"];
    }

    public function dlistbproduct()
    {
        $productb_id  = $this->request->getVar("productb_id");
        $data =$this->db->table("productb")
            ->where("productb_id", $productb_id)
            ->delete();

        $product_id = $this->request->getVar("product_id");
        $productb = $this->db->table("productb")
            ->join("product", "product.product_id=productb.productb_bundle", "left")
            ->where("productb.product_id", $product_id)
            ->orderBy("product.product_name ", "ASC")
            ->get();
        foreach ($productb->getResult() as $productb) { ?>
            <div class="lproductb"><?= $productb->product_name ?> (<?= $productb->productb_qty ?>)<span onclick="dlistbproduct(<?= $productb->productb_id ?>)" class="btn btn-xs btn-danger fa fa-close"></span></div>
        <?php }
    }


    public function listbproduct()
    {
        $productb_bundle = $this->request->getVar("bproduct_id");
        $productb_qty = $this->request->getVar("productb_qty");
        $product_id = $this->request->getVar("product_id");
        $input["productb_bundle"] = $productb_bundle;
        $input["productb_qty"] = $productb_qty;
        $input["product_id"] = $product_id;
        $this->db->table("productb")
            ->insert($input);

        $productb = $this->db->table("productb")
            ->join("product", "product.product_id=productb.productb_bundle", "left")
            ->where("productb.product_id", $product_id)
            ->orderBy("product.product_name ", "ASC")
            ->get();
        foreach ($productb->getResult() as $productb) { ?>
            <div class="lproductb"><?= $productb->product_name ?> (<?= $productb->productb_qty ?>)<span onclick="dlistbproduct(<?= $productb->productb_id ?>, <?= $product_id ?>)" class="btn btn-xs btn-danger fa fa-close"></span></div>
<?php }
    }
}
