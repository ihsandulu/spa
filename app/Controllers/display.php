<?php

namespace App\Controllers;



class display extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }

    public function index()
    {
        return view('display_v');
    }    

    public function room(){
        $room = $this->db->table("product")
        ->join("category","category.category_id=product.category_id","left")
        ->join("(SELECT product_lanjutan AS pid, product_name AS pname FROM product)productlanjutan", "productlanjutan.pid=product.product_id", "left")
        ->where("product.category_id","100")
        ->where("product_lanjutan","0")
        ->orderBy("product_urutan", "ASC")
        ->get();
        $status="secondary";
        $category_id=0;
        $noc=0;
        $warna=array("success","warning","info","primary","danger","secondary","dark","light");
        $nstatus="";
        foreach($room->getResult() as $room){
            if($room->product_status==0){
                if($room->transaction_id>0){
                    if(date("Y-m-d H:i:s")>=$room->product_start && date("Y-m-d H:i:s")<$room->product_bend){
                        $status="success"; $nstatus="TERISI";
                    }elseif(date("Y-m-d H:i:s")>=$room->product_bend && date("Y-m-d H:i:s")<=$room->product_end){
                        $status="warning"; $nstatus="AKAN HABIS";
                    }elseif(date("Y-m-d H:i:s")>$room->product_end&&date("Y-m-d H:i:s")<=date("Y-m-d H:i:s",strtotime($room->product_end." + 10 minute"))){
                        $status="danger"; $nstatus="SELESAI";
                    }else{
                        $status="secondary"; $nstatus="KOSONG";
                    }
                }else{
                    $status="secondary"; $nstatus="KOSONG";
                }	
            }elseif($room->product_status==1){
                 $status="light"; $nstatus="DIBERSIHKAN";
            }elseif($room->product_status==2){
                 $status="dark"; $nstatus="RUSAK";
            }else{
                 $status="secondary"; $nstatus="KOSONG";
            }				
            ?>
            <?php if($room->category_id!=$category_id){?>
            <div class="col-12">
                <!-- <h3 class="col-12 p-0"><span class="badge badge-<?=$warna[$noc++];?> col-12 category_name"><?=$room->category_name;?></span></h3> -->
                <h3 class="col-12 p-0"><span class="badge badge-<?=$warna[$noc++];?> col-12 "></span></h3>
            </div>
            <?php $category_id=$room->category_id;}?>
            <div class="col-lg-2 p-2 rounded">
                <div class="room rounded">
                    <div class="carddeckbg bg-<?=$status;?> inherit rounded">
                    </div>
                    <div class="carddeck rounded"><br/>
                        <div class="text rounded p-2 text-center row">
                            <div class="judul col-12"><?=$room->product_name;?></div>
                            <div class="subjudul1 text-<?=$status;?> col-12"><?=$room->customer_name;?></div>
                            <div class="col-12 p-1 d-grid"><btn onclick="roomstatus(<?=$room->product_id;?>,0);" class="btn btn-sm btn-<?=$status;?> btn-block"><?=$nstatus;?></btn></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
    }    

    public function roomstatus(){
        $input["product_start"]="0000-00-00 00:00:00";
        $input["product_bend"]="0000-00-00 00:00:00";
        $input["product_end"]="0000-00-00 00:00:00";
        $input["product_status"]=$this->request->getGet("product_status");
        $where["product_id"]=$this->request->getGet("product_id");
        $this->db->table("product")
        ->update($input,$where);
    }
}
