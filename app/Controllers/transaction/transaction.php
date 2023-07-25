<?php

namespace App\Controllers\transaction;


use App\Controllers\baseController;

class transaction extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\transaction\transaction_m();
        $data = $data->data();
        return view('transaction/transaction_v', $data);
    }


    public function print()
    {
        return view('transaction/transactionprint_v');
    }

    public function posisishift(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->orderBy("store_id","DESC")
        ->limit(1)
        ->get();
        $kas_type='keluar';//masuk,keluar
        foreach($builder->getResult() as $kas){
            $kas_type= $kas->store_posisishift;
            if($kas_type==""){$kas_type='keluar';}
        }
        echo $kas_type;
    }

    public function shift(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->orderBy("store_id","DESC")
        ->limit(1)
        ->get();
        $store_shift=1;//masuk,keluar
        $store_date=date("Y-m-d");
        foreach($builder->getResult() as $shift){
            $store_shift= $shift->store_shift;
            $store_date= $shift->store_date;
            $store_date= $shift->store_date;
        }
        
        //perhitungan shift
        if($store_shift==''||$store_date!=date("Y-m-d")){
            $store_shift=1;
        }else{
            $store_shift=$store_shift;
        }
        echo $store_shift;
    }

    public function kasmodal(){
        $data["message"]="";
        $kas_nominal=$this->request->getGet("kas_nominal");
        $kas_typeinput=$this->request->getGet("kas_type");//inputan
        $store_id=$this->request->getGet("store_id");
        $kas_shiftinput=$this->request->getGet("kas_shift");

        //cek kas realtime
        $storekas=$this->db->table("store")
        ->select("*,COUNT(store.store_id) AS count, modal.kas_id AS kas_id, store.modal_id AS modal_id")
        ->join("modal","modal.modal_id=store.modal_id")
        ->join("kas","kas.kas_id=modal.kas_id")
        ->where("store.store_id",$store_id)
        ->orderBy("store.store_id","DESC")
        ->limit(1);
        $store=$storekas->get();        
        $countstorekas=$store->getRow()->count;   
        $data["message"] = $this->db->getLastQuery(); 

        $kas_lasttype='keluar';//masuk,keluar
            
        if($countstorekas>0){
            foreach($store->getResult() as $kas){
                $modalid=$kas->modal_id;//id modal
                $modalawal=$kas->store_modal;//modal awal
                $kasrealtime=$kas->store_kas;//kas aktual
                $store_shift=$kas->store_shift;//shift ke berapa
                $store_date=$kas->store_date;//tgl shift yg sedang berlaku                

                $kas_lasttype= $kas->store_posisishift;//masuk, keluar

                //cek shift
                if($kas_lasttype==''||$store_date!=date("Y-m-d")){
                    $store_shift=1;
                }elseif($kas_lasttype=='keluar'&&$kas_typeinput=='masuk'){
                    $store_shift+=1;
                }else{
                    $store_shift=$store_shift;
                }
                
                // echo $kas_lasttype."-".$kas_typeinput."-".$store_date."-".date("Y-m-d");

                if(
                    ($kas_lasttype=='keluar'&&$kas_typeinput=='masuk')
                    ||(
                        $kas_lasttype=='masuk'&&$kas_typeinput=='masuk'
                        &&$store_date!=date("Y-m-d")
                        )
                    ){
                    //Keluar(Terakhir)-Masuk(Input) atau Masuk(Terakhir)-Masuk(Input) tapi Di Hari yang Sama

                    //insert kas modal awal
                    $input["store_id"]=session()->get("store_id");
                    $input["kas_modal"]= 1;
                    $input["kas_shift"]= $store_shift;
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_type"]= $kas_typeinput;
                    $input["account_id"]= '1';
                    $input["kas_description"]= "Modal Awal";
                    $input["kas_date"]= date("Y-m-d");
                    $builder=$this->db->table("kas")
                    ->insert($input);
                    $kas_id = $this->db->insertID();
                    $data["message"]="Store modal awal berhasil.";

                    //insert modal
                    $input2["kas_id"]=$kas_id;
                    $input2["store_id"]=session()->get("store_id");
                    $input2["account_id"]= '1';
                    $input2["modal_shift"]= $store_shift;
                    $input2["modal_nominal"]= $kasrealtime+$kas_nominal;
                    $input2["modal_date"]= date("Y-m-d");
                    $this->db->table("modal")
                    ->insert($input2);
                    $modal_id = $this->db->insertID();

                    //update store
                    $input1["modal_id"]= $modal_id;
                    $input1["store_kas"]= $kasrealtime+$kas_nominal;
                    $input1["store_modal"]= $kasrealtime+$kas_nominal;//nambah dari kas bukan dari modal
                    $input1["store_shift"]= $store_shift;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $input1["store_date"]= date("Y-m-d");
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);
                    // $data["message"] = $this->db->getLastQuery();

                }elseif($kas_lasttype=='masuk'&&$kas_typeinput=='masuk'){
                    //Masuk(Terakhir)-Masuk(Input) di Hari yang Sama

                    

                    //update kas modal awal
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_shift"]= $store_shift;
                    $where["kas_id"]= $kas->kas_id;
                    $where["store_id"]= $store_id;
                    $builder=$this->db->table("kas")
                    ->update($input,$where);
                    $kas_id = $kas->kas_id;
                    $data["message"]="Modal awal diupdate, ID=".$kas_id;

                    //update modal
                    $input2["modal_nominal"]= ($modalawal-$kasrealtime)+$kas_nominal;
                    $input2["modal_shift"]= $store_shift;
                    $input2["modal_date"]= date("Y-m-d");
                    $where2["modal_id"]= $modalid;
                    $this->db->table("modal")
                    ->update($input2,$where2);

                    //update store
                    $input1["store_kas"]= ($kasrealtime-$kasrealtime)+$kas_nominal;
                    $input1["store_modal"]= ($modalawal-$kasrealtime)+$kas_nominal;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);

                }elseif($kas_lasttype=='masuk'&&$kas_typeinput=='keluar'){
                    //Masuk(Terakhir)-Keluar(Input)

                    //input kas tariksetoran
                    $input["store_id"]=session()->get("store_id");
                    $input["kas_tariksetoran"]= 1;
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_shift"]= $store_shift;
                    $input["kas_type"]= $kas_typeinput;
                    $input["kas_description"]= "Penarikan Kas";
                    $input["kas_date"]= date("Y-m-d");
                    $input["account_id"]= '15';
                    $builder=$this->db->table("kas")
                    ->insert($input);
                    $kas_id = $this->db->insertID();
                    $data["message"]="Penarikan kas berhasil.";                   

                     //update store
                    $kastoko=$kasrealtime-$kas_nominal;
                    $input1["store_kas"]= $kastoko;
                    $input1["store_modal"]= $kastoko;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);
                }elseif($kas_lasttype=='keluar'&&$kas_typeinput=='keluar'){
                    //Masuk(Terakhir)-Keluar(Input) di Hari yang Sama
                    
                    

                    //update kas tarik setoran
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_shift"]= $store_shift;
                    $where["kas_id"]= $kas->kas_id;
                    $where["store_id"]= $store_id;
                    $builder=$this->db->table("kas")
                    ->update($input,$where);
                    $kas_id = $kas->kas_id;
                    $data["message"]="Modal Akhir diupdate, ID=".$kas_id;

                    // echo $kasrealtime."+".$kasrealtime."-".$kas_nominal;
                    //update store
                    $kastoko=($kasrealtime+$kasrealtime)-$kas_nominal;
                    $input1["store_kas"]= $kastoko;
                    $input1["store_modal"]= $kastoko;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);
                }
            }
        }else{
            $store_shift=1;
            $kasrealtime=0;
            //insert kas
            $input["store_id"]=session()->get("store_id");
            $input["kas_modal"]= 1;
            $input["kas_shift"]= $store_shift;
            $input["kas_nominal"]= $kas_nominal;
            $input["kas_type"]= $kas_typeinput;
            $input["kas_description"]= "Modal Awal";
            $input["kas_date"]= date("Y-m-d");
            $input["account_id"]= '1';
            $builder=$this->db->table("kas")
            ->insert($input);
            $kas_id = $this->db->insertID();
            $data["message"]="Store modal awal berhasil.";

            
            //insert modal
            $input2["kas_id"]=$kas_id;
            $input2["store_id"]=session()->get("store_id");
            $input2["modal_shift"]= $store_shift;
            $input2["modal_nominal"]= $kas_nominal;
            $input2["modal_date"]= date("Y-m-d");
            $input2["account_id"]= '1';
            $this->db->table("modal")
            ->insert($input2);
            $modal_id = $this->db->insertID();

            //update store
            $input1["store_kas"]= $kasrealtime+$kas_nominal;
            $input1["store_modal"]= $kasrealtime+$kas_nominal;//nambah dari kas bukan dari modal
            $input1["store_shift"]= $store_shift;
            $input1["modal_id"]= $modal_id;
            $input1["store_posisishift"]= 'masuk';
            $input1["store_date"]= date("Y-m-d");
            $where1["store_id"]= $store_id;
            $this->db->table("store")
            ->update($input1,$where1);
            // $data["message"] = $this->db->getLastQuery();
        }
        
        echo $data["message"];
        
    }

    public function nominalkas(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->get();
        $nominal=0;
        foreach($builder->getResult() as $store){
            $nominal=$store->store_kas;
        }
        echo $nominal;
    }

    public function modalawalkas(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->get();
        $modalawal=0;
        foreach($builder->getResult() as $store){
            $modalawal=$store->store_modal;
        }
        echo $modalawal;
    }

    public function listnota(){
        $builder=$this->db->table("transaction")
        ->where("store_id",session()->get("store_id"))
        ->where("transaction_status",$this->request->getGet("transaction_status"));
        if(isset($_GET["from"])&&$_GET["from"]!=""){
            $builder->where("transaction.transaction_date >=",$this->request->getGet("from"));
        }else{
            $builder->where("transaction.transaction_date",date("Y-m-d"));
        }

        if(isset($_GET["to"])&&$_GET["to"]!=""){
            $builder->where("transaction.transaction_date <=",$this->request->getGet("to"));
        }else{
            $builder->where("transaction.transaction_date",date("Y-m-d"));
        }
        $listnota= $builder
        ->get();
        foreach ($listnota->getResult() as $listnota) {
        ?>
            <button onclick="nota(<?=$listnota->transaction_id;?>);" class="btn btn-outline-secondary mb-2  btn-child" type="button"><small><?=$listnota->transaction_no;?></small></button>
        <?php
        }
    }

    public function createnota(){
        $input["transaction_date"] = date("Y-m-d");
        $input["transaction_no"] = "POS".date("YmdHis").session()->get("store_id");
        $input["cashier_id"] = session()->get("user_id");
        $input["store_id"] = session()->get("store_id");
        $input["transaction_status"] = 2;
        $input["transaction_shift"] = $this->request->getGet("transaction_shift");

        $builder = $this->db->table('transaction');
        $builder->insert($input);
        // $data["message"] = $this->db->getLastQuery();
        $transaction_id = $this->db->insertID();

        $data["message"] = $transaction_id;
        echo $data["message"];
    }

    public function insertnota(){
        $transaction_id=$this->request->getGet("transaction_id");

       
        $builder=$this->db->table("product")
        ->join("category","category.category_id=product.category_id","left");
        if(isset($_GET["product_id"])){
            $product_id=$this->request->getGet("product_id");
            $product=$builder->where("product_id",$product_id);
        }

        //insert via barcode
        if(isset($_GET["product_batch"])){
            $product_batch=$this->request->getGet("product_batch");
            $product=$builder->where("product_batch",$product_batch);
        }
        
        if(isset($_GET["transactiond_qty"])){
            $transactiond_qty=$this->request->getGet("transactiond_qty");
        }else{
            $transactiond_qty=1;
        }
        
        // produk berdurasi
        if(isset($_GET["start"])){
            $start=$this->request->getGet("start");
        }else{
            $start="0000-00-00 00:00:00";
        }

        // produk terkait posisi
        if(isset($_GET["xtherapist"])){
            $therapist=$this->request->getGet("xtherapist");
        }else{
            $therapist="0";
        }

        // diskon
        if(isset($_GET["xfoc"])){
            $foc=$this->request->getGet("xfoc");
        }else{
            $foc="0";
        }
        if(isset($_GET["xnominal"])){
            $nominal=$this->request->getGet("xnominal");
        }else{
            $nominal="0";
        }
        if(isset($_GET["xpercent"])){
            $percent=$this->request->getGet("xpercent");
        }else{
            $percent="0";
        }

        $message="";

       

        $pro=$product->get();
        $sell=$pro->getRow()->product_sell;
        $transactiond_lanjutan=$pro->getRow()->product_lanjutan;
        $transactiond_profittherapist=$pro->getRow()->product_profittherapist;
        $transactiond_profittrainer=$pro->getRow()->product_profittrainer;
        $transactiond_profitsales=$pro->getRow()->product_profitsales;
        $product_id=$pro->getRow()->product_id;
        $message.=$this->db->getLastQuery()."<br/>";

        if($pro->getNumRows()>0){            

            $where["transaction_id"] = $transaction_id;
            $where["product_id"] = $pro->getRow()->product_id;
            $where["store_id"] = session()->get("store_id");

            //cek lanjutan
            if($transactiond_lanjutan>0){
                $prolanjutan=$this->db->table("transactiond")
                ->where("product_id",$transactiond_lanjutan)
                ->get();
                foreach($prolanjutan->getResult() as $prolanjutan){
                    $start=$prolanjutan->transactiond_end;
                }
            }

            $durasi=$pro->getRow()->product_durasi;
            $dbend=$pro->getRow()->product_dbend;
            $transactiond_end=date("Y-m-d H:i:s",strtotime($start." + ".$durasi." minute"));
            $transactiond_bend=date("Y-m-d H:i:s",strtotime($transactiond_end." - ".$dbend." minute"));            
         
            $cari = $this->db->table('transactiond')
            ->where($where)
            ->get();
            $message.=$this->db->getLastQuery()."<br/>";

            $transactiond = $this->db->table('transactiond');
            if($cari->getNumRows()>0){
                foreach ($cari->getResult() as $cari) {
                    $qty=$cari->transactiond_qty;
                    $price=$cari->transactiond_price;
                    $input["transactiond_qty"] = $qty+$transactiond_qty;
                    $input["transactiond_price"] = $price+$sell;
                    $input["transactiond_start"]=$start;
                    $input["transactiond_bend"]=$transactiond_bend;
                    $input["transactiond_end"]=$transactiond_end;
                    $input["user_id"]=$therapist;
                    $input["transactiond_foc"]=$foc;
                    $input["transactiond_nominal"]=$nominal;
                    $input["transactiond_percent"]=$percent;
                
                    $input["transactiond_lanjutan"]=$transactiond_lanjutan;
                    $input["transactiond_profittherapist"]=$transactiond_profittherapist;
                    $input["transactiond_profittrainer"]=$transactiond_profittrainer;
                    $input["transactiond_profitsales"]=$transactiond_profitsales;

                    $transactiond->update($input,$where);
                    $message.=$this->db->getLastQuery()."<br/>";

                    if($pro->getRow()->product_type==0||$pro->getRow()->category_unique==1){
                        $where1["product_id"] = $pro->getRow()->product_id;
                        if($pro->getRow()->product_type==0){
                            $product = $this->db->table('product');
                            $product_stock=$product->getWhere($where1)->getRow()->product_stock;
                            $product_stock=$product_stock-$transactiond_qty;
                            $input1["product_stock"] = $product_stock;
                        }
                        if($pro->getRow()->category_unique==1){
                            $product = $this->db->table('product');
                            $input1["transaction_id"] = $transaction_id;
                            $input1["product_bend"] = $transactiond_bend;
                            $input1["product_end"] = $transactiond_end;
                        }
                        $product->update($input1,$where1);
                        $message.=$this->db->getLastQuery()."<br/>";
                    }
                }
            }else{
                $where["store_id"]=session()->get("store_id");
                $where["transactiond_qty"] = $transactiond_qty;
                // $where["room_id"] = $room_id;
                $where["transactiond_price"] = $sell*$transactiond_qty;
                $where["transactiond_start"]=$start;
                $where["transactiond_bend"]=$transactiond_bend;
                $where["transactiond_end"]=$transactiond_end;
                $where["user_id"]=$therapist;
                $where["transactiond_foc"]=$foc;
                $where["transactiond_nominal"]=$nominal;
                $where["transactiond_percent"]=$percent;
                
                $where["transactiond_lanjutan"]=$transactiond_lanjutan;
                $where["transactiond_profittherapist"]=$transactiond_profittherapist;
                $where["transactiond_profittrainer"]=$transactiond_profittrainer;
                $where["transactiond_profitsales"]=$transactiond_profitsales;

                $transactiond->insert($where);
                $transactiond_id = $this->db->insertID();
                $data["message"] = $this->db->getLastQuery();
                $message.=$this->db->getLastQuery()."<br/>";

                if($pro->getRow()->product_type==0||$pro->getRow()->category_unique==1){
                    $where1["product_id"] = $pro->getRow()->product_id;
                    if($pro->getRow()->product_type==0){
                        $product = $this->db->table('product');
                        $product_stock=$product->getWhere($where1)->getRow()->product_stock;
                        $product_stock=$product_stock-$transactiond_qty;
                        $input1["product_stock"] = $product_stock;
                    }
                    if($pro->getRow()->category_unique==1){
                        $product = $this->db->table('product');
                        $input1["transaction_id"] = $transaction_id;
                        $input1["product_bend"] = $transactiond_bend;
                        $input1["product_end"] = $transactiond_end;
                    }
                    $product->update($input1,$where1);
                    $message.=$this->db->getLastQuery()."<br/>";
                }
            }

            $data["message"] = $message;
            // $data["message"] = $transactiond_qty;
        }else{
            $data["message"] = 0;    
        }
        echo $data["message"];
    }

    public function deletenota(){
        $transaction_id=$this->request->getGet("transaction_id");
        $input["transaction_id"] = $transaction_id;

        $builder = $this->db->table('transactiond');
        $transactiond=$builder
        ->select("product_id,SUM(transactiond_qty)AS qty")
        ->where("transaction_id",$transaction_id)
        ->groupBy("product_id")
        ->get();
        foreach ($transactiond->getResult() as $transactiond) {
            $where2["product_id"] = $transactiond->product_id;
            $product = $this->db->table('product');
            $product_stock=$product->getWhere($where2)->getRow()->product_stock;
            $product_stock=$product_stock+$transactiond->qty;
            $input2["product_stock"] = $product_stock;
            $product->update($input2,$where2);
            // echo $this->db->getLastQuery()."<br/>";
        }
        $builder->delete($input);

        $builder = $this->db->table('transaction');
        $builder->delete($input);
        // $data["message"] = $this->db->getLastQuery();

        $data["message"] = $transaction_id;
        echo $data["message"];

        //delete kas
        $input1["transaction_id"]= $transaction_id;
        $builder=$this->db->table("kas")
        ->delete($input1);

        // echo $this->db->getLastQuery();
    }


    public function deletetransactiond(){
        $transaction_id=$this->request->getGet("transaction_id");
        $product_id=$this->request->getGet("product_id");
        $product_qty=$this->request->getGet("product_qty");
        $input["transaction_id"] = $transaction_id;
        $input["product_id"] = $product_id;

        $builder = $this->db->table('transactiond');
        $builder->delete($input);
        $data["message"] = $this->db->getLastQuery();

        echo $data["message"];

        $where1["product_id"] = $product_id;
        $product = $this->db->table('product');
        $product_stock=$product->getWhere($where1)->getRow()->product_stock;
        $product_stock=$product_stock+$product_qty;
        $input1["product_stock"] = $product_stock;
        $input1["transaction_id"] = "0";
        $input1["product_bend"] = null;
        $input1["product_end"] = null;
        $product->update($input1,$where1);

    }

    public function updateqty(){
        $transactiond_id=$this->request->getGet("transactiond_id");
        $transaction_id=$this->request->getGet("transaction_id");
        $type=$this->request->getGet("type");
        $transactiond_qty=$this->request->getGet("transactiond_qty");
        // $room_id=$this->request->getGet("room_id");

        

         //insert produk berdurasi
        if(isset($_GET["start"])){
            $start=$this->request->getGet("start");
        }else{
            $start="0000-00-00 00:00:00";
        }

        

        //insert produk terkait posisi
        if(isset($_GET["xtherapist"])){
            $therapist=$this->request->getGet("xtherapist");
        }else{
            $therapist="0";
        }

        //insert diskon
        if(isset($_GET["xfoc"])){
            $foc=$this->request->getGet("xfoc");
        }else{
            $foc="0";
        }
        if(isset($_GET["xnominal"])){
            $nominal=$this->request->getGet("xnominal");
        }else{
            $nominal="0";
        }
        if(isset($_GET["xpercent"])){
            $percent=$this->request->getGet("xpercent");
        }else{
            $percent="0";
        }

        $input["transactiond_id"] = $transactiond_id;
        //cek qty
        $transactiond = $this->db->table('transactiond')
        ->join("product","product.product_id=transactiond.product_id","left")
        ->join("category","category.category_id=product.category_id","left")
        ->where($input)
        ->get();
        foreach ($transactiond->getResult() as $transactiond) {
            //cek produk lanjutan   
            $transactiond_lanjutan=$transactiond->product_lanjutan;
            $transactiond_profittherapist=$transactiond->product_profittherapist;
            $transactiond_profittrainer=$transactiond->product_profittrainer;
            $transactiond_profitsales=$transactiond->product_profitsales;
            
            $parentnya=$this->db->table("transactiond")
            ->where("transaction_id",$transaction_id)
            ->where("product_id",$transactiond_lanjutan)
            ->orderBy("transactiond_id","DESC")
            ->limit(1)
            ->get();
            foreach($parentnya->getResult() as $parentnya){
                $start=$parentnya->transactiond_end;
            }            
            $data["message"]=$start;

            $sell=$transactiond->product_sell*$transactiond_qty;
            $product_id=$transactiond->product_id;
        
            $qty=$transactiond->transactiond_qty;
            $price=$transactiond->transactiond_price;
            $data["message"] = $qty;
            if($type=="tambah"){
                $qty+=$transactiond_qty;
                $price+=$sell;
            }
            if($type=="kurang"){
                $qty-=$transactiond_qty;
                $price-=$sell;
            }
            if($type=="update"){
                $qty=$transactiond_qty;
                $price=$sell;
            }

            $durasi=$transactiond->product_durasi*$qty;
            $dbend=$transactiond->product_dbend;
            $transactiond_end=date("Y-m-d H:i:s",strtotime($start." + ".$durasi." minute"));
            $transactiond_bend=date("Y-m-d H:i:s",strtotime($transactiond_end." - ".$dbend." minute"));

            $input2["transactiond_qty"] = $qty;
            $input2["transactiond_price"] = $price;
            $input2["transactiond_start"]=$start;
            $input2["transactiond_bend"]=$transactiond_bend;
            $input2["transactiond_end"]=$transactiond_end;
            $input2["user_id"]=$therapist;
            $input2["transactiond_foc"]=$foc;
            $input2["transactiond_nominal"]=$nominal;
            $input2["transactiond_percent"]=$percent;            
                
            $input2["transactiond_lanjutan"]=$transactiond_lanjutan;
            $input2["transactiond_profittherapist"]=$transactiond_profittherapist;
            $input2["transactiond_profittrainer"]=$transactiond_profittrainer;
            $input2["transactiond_profitsales"]=$transactiond_profitsales;

            // $input2["room_id"] = $room_id;
            $where2["transactiond_id"] = $transactiond_id;
            $builder = $this->db->table('transactiond');
            $builder->update($input2,$where2);
            $data["message"] = $this->db->getLastQuery();

            // $data["message"] = $transactiond_id;
            if($transactiond->product_type==0||$transactiond->category_unique==1){
                if($transactiond_lanjutan>0){
                    $proid=$transactiond_lanjutan;
                }else{
                    $proid=$product_id;
                }
                $wherep["product_id"] = $proid;
                $product = $this->db->table('product');
                $product_stock=$product->getWhere($wherep)->getRow()->product_stock;
                if($type=="tambah"){                
                    $product_stock=$product_stock-$transactiond_qty;
                }
                if($type=="kurang"){        
                    $product_stock=$product_stock+$transactiond_qty;
                }
                if($type=="update"){
                    $product_stock=$product_stock+$transactiond->transactiond_qty-$transactiond_qty;
                }
                $inputp["product_stock"] = $product_stock;
                
                if($transactiond->category_unique==1){
                    $inputp["product_bend"] = $transactiond_bend;
                    $inputp["product_end"] = $transactiond_end;
                }

                $product->update($inputp,$wherep);
                // $data["message"] = $this->db->getLastQuery();
            }          
        }
        echo $data["message"];
    }

    public function pelunasan(){
        $account_id = $this->request->getGet("account_id");
        $transaction_id = $this->request->getGet("transaction_id");
        $transaction_no = $this->request->getGet("transaction_no");
        $transaction_bill = $this->request->getGet("transaction_bill");
        $transaction_pay = $this->request->getGet("transaction_pay");
        $transaction_change = $this->request->getGet("transaction_change");
        $kas_shift = $this->request->getGet("shift");
        
        //metodepembayaran   
        $transaction_nominalcash = $this->request->getGet("transaction_nominalcash");
        $transaction_numbercard1 = $this->request->getGet("transaction_numbercard1");
        $transaction_nominalcard1 = $this->request->getGet("transaction_nominalcard1");
        $transaction_numbercard2 = $this->request->getGet("transaction_numbercard2");
        $transaction_nominalcard2 = $this->request->getGet("transaction_nominalcard2");

        $transaction_pending = $this->request->getGet("transaction_pending");
        if($transaction_pending>0){
            $transaction_status=2;
        }else{
            $transaction_status=0;
        }
        echo $transaction_status;


        

        $input["transaction_bill"]=$transaction_bill;
        $input["transaction_pay"]=$transaction_pay;
        $input["transaction_change"]=$transaction_change;
        $input["transaction_status"]=$transaction_status;
        $input["account_id"]=$account_id;
        //metodepembayaran        
        $input["transaction_nominalcash"]=$transaction_nominalcash;
        $input["transaction_numbercard1"]=$transaction_numbercard1;
        $input["transaction_nominalcard1"]=$transaction_nominalcard1;
        $input["transaction_numbercard2"]=$transaction_numbercard2;
        $input["transaction_nominalcard2"]=$transaction_nominalcard2;

        $input["transaction_pending"]=$transaction_pending;

        $this->db->table("transaction")
        ->where("transaction_id",$transaction_id)
        ->update($input);
        // echo 0;
        // echo $this->db->getLastQuery();

        $cekkasdouble=$this->db->table("kas")
        ->where("store_id",session()->get("store_id"))
        ->where("kas_shift",$kas_shift)
        ->where("transaction_id",$transaction_id)
        ->where("kas_type",'masuk')
        ->where("account_id",$account_id)
        ->where("kas_date",date("Y-m-d"))
        ->get();
        $numRows = $cekkasdouble->getNumRows();
        if($numRows>0){            
            if ($cekkasdouble->getResult()) {
                $kas_id = $cekkasdouble->getRow()->kas_id;
                //update kas
                $where1["kas_id"]=$kas_id;
                $input1["store_id"]=session()->get("store_id");
                $input1["kas_shift"]= $kas_shift;
                $input1["transaction_id"]= $transaction_id;
                $input1["kas_nominal"]= $transaction_bill;
                $input1["kas_type"]= 'masuk';
                $input1["account_id"]= $account_id;
                $input1["kas_description"]= "Pembayaran ".$transaction_no;
                $input1["kas_date"]= date("Y-m-d");
                $builder=$this->db->table("kas")
                ->update($input1,$where1);
            } else {
                echo "kas id tidak ditemukan";die;
            }            
        }else{
            //insert kas
            $input11["store_id"]=session()->get("store_id");
            $input11["kas_shift"]= $kas_shift;
            $input11["transaction_id"]= $transaction_id;
            $input11["kas_nominal"]= $transaction_bill;
            $input11["kas_type"]= 'masuk';
            $input11["account_id"]= $account_id;
            $input11["kas_description"]= "Pembayaran ".$transaction_no;
            $input11["kas_date"]= date("Y-m-d");
            $builder=$this->db->table("kas")
            ->insert($input11);
        }

        /* //kas
        $kas=$this->db->table("kas")
        ->where("transaction_id",$transaction_id);
        // ->get();
        $jmlkas=$kas->countAllResults();
        // echo $this->db->getLastQuery();die;
        // echo $jmlkas;die;
        if($jmlkas==0){
            //insert kas
            $input1["store_id"]=session()->get("store_id");
            $input1["kas_shift"]= $kas_shift;
            $input1["transaction_id"]= $transaction_id;
            $input1["kas_nominal"]= $transaction_bill;
            $input1["kas_type"]= 'masuk';
            $input1["account_id"]= $account_id;
            $input1["kas_description"]= "Pembayaran ".$transaction_no;
            $input1["kas_date"]= date("Y-m-d");
            $builder=$this->db->table("kas")
            ->insert($input1);
        }else{
            $input1["account_id"]= $account_id;
            $where1["transaction_id"]= $transaction_id;
            $builder=$this->db->table("kas")
            ->update($input1,$where1);

        } */
        

        // echo $this->db->getLastQuery();

        //update store
        //cek kas realtime
        $store=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->orderBy("store_id","DESC")
        ->limit(1)
        ->get()
        ->getRow();
        $kasrealtime=$store->store_kas;//kas aktual
        $input2["store_kas"]= $kasrealtime+$transaction_bill;
        $input2["store_shift"]= $kas_shift;
        $input2["store_posisishift"]= 'masuk';
        $input2["store_date"]= date("Y-m-d");
        $where2["store_id"]= session()->get("store_id");
        $this->db->table("store")
        ->update($input2,$where2);
    }

    public function updatestatus(){
        $input["transaction_status"]=$this->request->getGet("transaction_status");
        $this->db->table("transaction")
        ->where("transaction_id",$this->request->getGet("transaction_id"))
        ->update($input);
        echo $data["message"]=$input["transaction_status"];
    }

    public function cekstatus(){
        $status=$this->db->table("transaction")
        ->where("transaction_id",$this->request->getGet("transaction_id"))
        ->get();
        $data["message"]=2;
        foreach ($status->getResult() as $status) {
            $data["message"]=$status->transaction_status;
        }
        echo $data["message"];
    }


    public function tamu(){
        $input["transaction_tamu"]=$this->request->getGet("transaction_tamu");
        $where["transaction_id"]=$this->request->getGet("transaction_id");
        $this->db->table("transaction")
        ->update($input,$where);
        // echo $this->db->getLastQuery();
    }


    public function nota(){
        $transaction=$this->db->table("transaction")
        ->join("member","member.member_id=transaction.member_id","left")
        ->where("transaction_id",$this->request->getGet("transaction_id"))
        ->get();
        foreach ($transaction->getResult() as $transaction) {
            if($transaction->transaction_status==0){$iconprint="";}else{$iconprint="hide";}
        ?>
        <div class="row"> 
            <div class="col-9">
                NOTA : <i id="transactionno"><?=$transaction->transaction_no;?></i>
                <?php if($transaction->member_id>0){?>( <?=$transaction->member_name;?> )<?php }?>
                <input type="hidden" id="customer_name" value="<?=$transaction->member_name;?>"/>
            </div>
            <div class="col-3 text-right">
                <button onclick="print(<?=$transaction->transaction_id;?>);" id="printicon" class="btn btn-warning btn-xs btn-right fa fa-print mb-2" type="button"></button>
                <?php 
                if (
                    (
                        isset(session()->get("position_administrator")[0][0]) 
                        && (
                            session()->get("position_administrator") == "1" 
                            || session()->get("position_administrator") == "2"
                        )
                    ) ||
                    (
                        isset(session()->get("halaman")['13']['act_delete']) 
                        && session()->get("halaman")['13']['act_delete'] == "1"
                    )
                ) { ?>
                <button onclick="deletenota(<?=$transaction->transaction_id;?>);" class="btn btn-danger btn-xs btn-right fa fa-close mb-2" type="button"></button>
                <?php }?>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-1">
                        <button id="btntamu" onclick="showtamu()" type="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button>
                    </div>
                    <div class="col-11">
                        Nama Tamu : <input readonly class="form-group borderbawah" type="text" id="transaction_tamu" value="<?=$transaction->transaction_tamu;?>"/>
                        <button onclick="hidetamu()" id="btntamuok" type="button" class="btn btn-sm btn-primary hide"><i class="fa fa-check"></i></button>
                    </div>
                    <script>
                        function showtamu(){
                            $("#transaction_tamu").removeAttr("readonly");
                            $("#transaction_tamu").focus();
                            $("#btntamuok").removeClass("hide");
                            $("#btntamu").addClass("hide");
                        }
                        function hidetamu(){
                            let transaction_tamu=$("#transaction_tamu").val();
                            $("#transaction_tamu").attr("readonly","readonly");
                            $("#btntamuok").addClass("hide");
                            $("#btntamu").removeClass("hide");
                            // alert("<?=base_url();?>/tamu?transaction_id=<?=$transaction->transaction_id;?>&transaction_tamu="+transaction_tamu);
                            $.get("<?=base_url();?>/tamu",{transaction_id:<?=$transaction->transaction_id;?>,transaction_tamu:transaction_tamu})
                            .done(function(data){
                                // alert(data);
                            });
                        }
                        $(window).ready(function() {
                            $("#transaction_tamu").on("keyup", function (event) {
                                var keyPressed = event.keyCode || event.which;
                                if (keyPressed === 13) {
                                    alert("Nama Tamu telah diupdate.");
                                    hidetamu();
                                    event.preventDefault();
                                    return false;
                                }
                            });
                        });
                        </script>
                </div>
            </div>
        </div>
        <div>
            <input type="hidden" id="transaction_status" value="<?=$transaction->transaction_status;?>"/>
            <input type="hidden" id="transaction_id" value="<?=$transaction->transaction_id;?>"/>
            <!-- <input type="hidden" id="transactiond_id" value="0"/> -->
            <input type="hidden" id="transaction_no" value="<?=$transaction->transaction_no;?>"/>
            <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usr = $this->db
                        ->table("transactiond")
                        ->select("*,SUM(transactiond_qty)AS qty, SUM(transactiond_price)AS price, transactiond.transaction_id as transaction_id")
                        ->join("product", "product.product_id=transactiond.product_id", "left")
                        ->join("user", "user.user_id=transactiond.user_id", "left")
                        ->join("category", "category.category_id=product.category_id", "left")
                        ->join("unit", "unit.unit_id=product.unit_id", "left")
                        ->where("product.store_id",session()->get("store_id"))
                        ->where("transactiond.transaction_id",$this->request->getGet("transaction_id"))
                        ->groupBy("transactiond.product_id")
                        ->orderBy("product_name", "ASC")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    $tprice=0;
                    foreach ($usr->getResult() as $usr) { 
                        if($usr->product_durasi>0&&$usr->product_lanjutan==0){$start=1;}else{$start=0;}
                        if($usr->position_id>0){$therapist=1;}else{$therapist=0;}
                        if($usr->transactiond_foc>0){$diskon=$usr->transactiond_price;$kdis="FOC";}else 
                        if($usr->transactiond_nominal>0){$diskon=$usr->transactiond_nominal;$kdis="Nominal";}else 
                        if($usr->transactiond_percent>0){$diskon=$usr->transactiond_percent/100*$usr->transactiond_price;$kdis=$usr->transactiond_percent."%";}else 
                        {$diskon=0;$kdis="";}

                        if($usr->product_start==null){$usr->product_start="0000-00-00 00:00:00";}
                        if($usr->user_id==null){$usr->user_id=0;}
                        
                        $qty=$usr->qty; 
                        $price=$usr->price; 
                        $stprice=$price-$diskon;
                        $tprice+=$stprice; 
                        ?>
                        <tr class="">
                            <?php if (!isset($_GET["report"])) { ?>
                                <td style="padding-left:0px; padding-right:0px;"> 
                                    <?php 
                                    if (
                                        (
                                            isset(session()->get("position_administrator")[0][0]) 
                                            && (
                                                session()->get("position_administrator") == "1" 
                                                || session()->get("position_administrator") == "2"
                                            )
                                        ) ||
                                        (
                                            isset(session()->get("halaman")['13']['act_delete']) 
                                            && session()->get("halaman")['13']['act_delete'] == "1"
                                        )
                                    ) { ?>
                                        <button id="del<?= $usr->transactiond_id; ?>" class="btn btn-xs btn-danger delete m-2" onclick="deletetransactiond(<?= $usr->transaction_id; ?>,<?= $usr->product_id; ?>,<?= $usr->qty; ?>);" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                    <?php }?>
                                </td>
                            <?php } ?>
                            <td class="text-small0"><?= $no++; ?></td>
                            <td class="text-small0">
                                <?php 
                                $statusroom="secondary";
                                if($usr->product_durasi>0&&$usr->product_lanjutan==0){
                                    if(date("Y-m-d H:i:s")>$usr->product_start&&date("Y-m-d H:i:s")<$usr->product_bend){$statusroom="success";}
                                    elseif(date("Y-m-d H:i:s")>=$usr->product_bend&&date("Y-m-d H:i:s")<=$usr->product_end){$statusroom="warning";}
                                    else{
                                        $statusroom="danger";
                                    }
                                }
                                ?>
                                <div class="mb-10">
                                    <?php if($usr->product_durasi>0&&$usr->product_lanjutan==0){?>
                                        <span class="bg-<?=$statusroom;?> dot spinner-grow" >&nbsp;</span> 
                                    <?php }?>
                                    <?= $usr->product_name; ?>
                                </div>
                                <?php if($usr->user_id>0){?>
                                    <div class="mb-10 text-primary">Therapist:<?= $usr->user_name; ?></div>
                                <?php }?>  
                                <?php if($usr->product_durasi>0&&$usr->product_lanjutan==0){?>
                                    <div class="text-secondary mb-10">(<b class="text-success">Start:</b> <?= $usr->product_start; ?>)</div>
                                    <div class="text-secondary mb-10">(<b class="text-warning">Bend:</b> <?= $usr->product_bend; ?>)</div>
                                    <div class="text-secondary m-0">(<b class="text-danger">End:</b> <?= $usr->product_end; ?>)</div>
                                <?php }?>
                            </td>                            
                            <td class="text-small0">
                                <?php 
                                if (
                                    (
                                        isset(session()->get("position_administrator")[0][0]) 
                                        && (
                                            session()->get("position_administrator") == "1" 
                                            || session()->get("position_administrator") == "2"
                                        )
                                    ) ||
                                    (
                                        isset(session()->get("halaman")['13']['act_update']) 
                                        && session()->get("halaman")['13']['act_update'] == "1"
                                    )
                                ) { ?>
                                    <?php if($qty>1&&$usr->category_unique==0){?>
                                    <i onclick="updateqty(<?= $usr->transactiond_id; ?>,'kurang','1')" class="fa fa-minus text-small text-danger pointer"></i> 
                                    <?php }?>

                                    <button type="button" class="btn btn-xs btn-warning" onclick="insertjmlnota(<?= $usr->transactiond_id; ?>,<?= $qty; ?>,<?= $usr->product_id; ?>,<?=$start;?>,<?=$therapist;?>,'<?= $usr->product_start; ?>',<?= $usr->user_id; ?>,<?= $usr->transactiond_foc; ?>,<?= $usr->transactiond_nominal; ?>,<?= $usr->transactiond_percent; ?>);">
                                     <?= number_format($qty,0,",",".") ?> <?= $usr->unit_name; ?> 
                                    </button>

                                    <?php if(($usr->product_stock>0&&$usr->category_unique==0)||($usr->product_type==1&&$usr->category_unique==0)){?>
                                        <i onclick="updateqty(<?= $usr->transactiond_id; ?>,'tambah','1')" class="fa fa-plus text-small text-success pointer"></i>
                                    <?php }?>
                                <?php }else{?>
                                    <?= number_format($qty,0,",",".") ?> <?= $usr->unit_name; ?>     
                                <?php }?>   
                            </td>
                            <td class="text-small0"><?= number_format($price,0,",",".") ?></td>
                            <td class="text-small0"><div style="font-size:10px; color:red; margin-bottom:0px;"><?=$kdis;?></div><?=number_format($diskon,0,",",".");?></td>
                            <td class="text-small0"><?= number_format($stprice,0,",",".") ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="6">Total</th>
                        <th>
                            <?= number_format($tprice,0,",","."); ?>
                            <input type="hidden" id="tagihan" value="<?=$tprice;?>"/>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="6">
                            <?php 
                            if (
                                (
                                    isset(session()->get("position_administrator")[0][0]) 
                                    && (
                                        session()->get("position_administrator") == "1" 
                                        || session()->get("position_administrator") == "2"
                                    )
                                ) ||
                                (
                                    isset(session()->get("halaman")['13']['act_create']) 
                                    && session()->get("halaman")['13']['act_create'] == "1"
                                )
                            ) { ?>
                            <button class="btn btn-md btn-success" onclick="bayar();"><span class="fa fa-money" style="color:white;"></span>  Bayar</button>        
                            <?php }else{echo "Bayar";}?>                    
                            <input type="hidden" id="bayarannya" value="<?=$transaction->transaction_pay;?>"/>
                        </th>
                        <th class="dibayar"><?=$transaction->transaction_pay;?></th>
                    </tr>
                    <tr>
                        <th colspan="6">
                            Kembalian          
                            <input type="hidden" id="kembaliannya" value="<?=$transaction->transaction_change;?>"/>
                            <input type="hidden" id="status" value="<?=$transaction->transaction_status;?>"/>
                        </th>
                        <th class="kembalian"><?=$transaction->transaction_change;?></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        }
    }

    public function listproductgambar(){
        $builder = $this->db->table("product");        
        $builder->select("*,product.category_id as category_id");
        $builder->join("category","category.category_id=product.category_id","left");
        $builder->join("(SELECT * FROM transaction WHERE transaction_status='2')transaction", "transaction.transaction_id=product.transaction_id", "left");
         if($this->request->getGet("product_name")!=""){
            $builder->like("product.product_name",$this->request->getGet("product_name"),"BOTH");
        }
        $builder->where("product.store_id",session()->get("store_id"));
        $builder->orderBy("category.category_name","ASC");
        $builder->orderBy("product.product_name","ASC");
        $product=$builder
        ->limit(20)
        ->get();
        $category_id=0;
        $noc=0;
        $warna=array("success","warning","info","primary","danger","secondary","dark","light");
        $isiinsertnota ="";
        foreach ($product->getResult() as $product) {
            if($product->product_durasi>0&&$product->product_lanjutan==0){$start=1;}else{$start=0;}
            if($product->position_id>0){$therapist=1;}else{$therapist=0;}
        ?>
        <?php 
        if (
            (
                isset(session()->get("position_administrator")[0][0]) 
                && (
                    session()->get("position_administrator") == "1" 
                    || session()->get("position_administrator") == "2"
                )
            ) ||
            (
                isset(session()->get("halaman")['13']['act_create']) 
                && session()->get("halaman")['13']['act_create'] == "1"
            )
        ) {
            if(($product->product_stock>0&&$product->product_type==0)||($product->product_type==1&&$product->product_lanjutan==0)){
                $insertnota = "insertjmlnota(0,1,".$product->product_id.",".$start.",".$therapist.",'0000-00-00 00:00:00',0,0,0,0)";
                $disabled="";
            }elseif($product->product_lanjutan>0){
                $insertnota = "toast('Info', 'Produk Induk Tidak Ditemukan!')";
                $disabled="disabled";
            }else{
                $insertnota = "toast('Info Stock', 'Stock Kosong!')";
                $disabled="disabled";
            }

            if($product->transaction_status==2&&$product->category_unique==1){$disabled="disabled";$insertnota = "toast('Info Status', 'Tidak Tersedia!')";}

            $isiinsertnota = "insertjmlnota(0,1,".$product->product_id.",".$start.",".$therapist.",'0000-00-00 00:00:00',0,0,0,0)";

        }else{$insertnota =""; $disabled="disabled";}
        
        if($product->product_picture==""){
            $gambar="noimagespa.jpg";
        }else{
            $gambar=$product->product_picture;
        }

        if($product->product_lanjutan>0){
            $blanjutan="blanjutan";
        }else{
           $blanjutan="";
        }
        ?>
        <?php if($product->category_id!=$category_id){?>
        <div class="col-12">
            <h3 class="col-12 p-0"><span class="badge badge-<?=$warna[$noc++];?> col-12"><?=$product->category_name;?></span></h3>
        </div>
        <?php $category_id=$product->category_id;}?>
        <input type="hidden" class="ilanjutan<?=$product->product_lanjutan;?>" value="<?=$isiinsertnota;?>"/>
        <div class="col-3 divimg_product <?=$disabled;?> <?=$blanjutan;?> planjutan<?=$product->product_lanjutan;?>" onclick="<?=$insertnota;?>" >
            <figure class="caption-1 pointer">
                <img src="<?=base_url("images/product_picture/". $gambar);?>" alt="" class="w-100 card-img-top  img_product">
                <figcaption class="px-5 py-4 text-center text-light">
                    <h2 class="h5 font-weight-bold mb-0 text-small1 text-light figcaption"><?=$product->product_name;?></h2>
                    <p class="text-small2 figcaption"><?=number_format($product->product_sell,0,",",".");?></p>
                </figcaption>
            </figure>
        </div>
        <?php }
    }

    public function listproductlist(){
       ?>
            <!-- <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%"> -->
                <table id="dataTable" class="table table-condensed table-hover ">
                <thead class="">
                    <tr>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Ube</th>
                        <th>Batch</th>
                        <th>Exp Date</th>
                        <th>Stock</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $builder = $this->db->table("product");
                    $usr1 = $builder->join("category", "category.category_id=product.category_id", "left")
                        ->join("(SELECT * FROM transaction WHERE transaction_status='2')transaction", "transaction.transaction_id=product.transaction_id", "left")
                        ->join("unit", "unit.unit_id=product.unit_id", "left")
                        ->join("store", "store.store_id=product.store_id", "left")
                        ->where("product.store_id",session()->get("store_id"));
                    if($this->request->getGet("product_name")!=""){
                        $usr1->like("product.product_name",$this->request->getGet("product_name"),"BOTH");
                    }
                    $usr=$usr1->orderBy("product_name", "ASC")
                        ->limit(20)
                        ->get();
                    // echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) { 
                        // if($usr->product_durasi>0&&$usr->product_lanjutan==0){$start=1;}else{$start=0;}
                        if($usr->product_durasi>0&&$usr->product_lanjutan==0){$start=1;}else{$start=0;}
                        if($usr->position_id>0){$therapist=1;}else{$therapist=0;}
                    ?>
                    <?php 
                        if (
                            (
                                isset(session()->get("position_administrator")[0][0]) 
                                && (
                                    session()->get("position_administrator") == "1" 
                                    || session()->get("position_administrator") == "2"
                                )
                            ) ||
                            (
                                isset(session()->get("halaman")['13']['act_create']) 
                                && session()->get("halaman")['13']['act_create'] == "1"
                            )
                        ) {
                            if(($usr->product_stock>0&&$usr->product_type==0)||($usr->product_type==1&&$usr->product_lanjutan==0)){
                                $insertnota = "insertjmlnota(0,1,".$usr->product_id.",".$start.",".$therapist.",'0000-00-00 00:00:00',0,0,0,0)";
                                $disabled="";
                            }elseif($usr->product_lanjutan>0){
                                $insertnota = "toast('Info', 'Produk Induk Tidak Ditemukan!')";
                                $disabled="disabled";
                            }else{
                                $insertnota = "toast('Info Stock', 'Stock Kosong!')";
                                $disabled="disabled";
                            }

                            if($usr->transaction_status==2&&$usr->category_unique==1){$disabled="disabled";$insertnota = "toast('Info Status', 'Tidak Tersedia!')";}

                        }else{$insertnota ="";}?>
                        <tr class="pointer <?=$disabled;?> planjutan<?=$usr->product_lanjutan;?>" onclick="<?=$insertnota;?>">                            
                            <td><?= $usr->category_name; ?></td>
                            <td><?= $usr->product_name; ?></td>
                            <td><?= $usr->product_ube; ?></td>
                            <td><?= $usr->product_batch; ?></td>
                            <td><?= $usr->product_expiredate; ?></td>
                            <?php 
                            $limit=$usr->product_countlimit; 
                            $stock=$usr->product_stock;
                            if($limit>=$stock){$alstock="danger";}else{$alstock="default";}
                            ?>
                            <td><span class="text-<?=$alstock;?>"><?= ($usr->product_type==0)?number_format($stock,0,",","."):""; ?></span> <?= ($usr->product_type==0)?$usr->unit_name:""; ?></td>
                            <?php 
                            $buy=$usr->product_buy; 
                            $sell=$usr->product_sell;
                            $margin=$sell-$buy;
                            ?>
                            <td><?= number_format($sell,0,",","."); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
       <?php
    }

    public function listmember(){
       ?>
        <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
            <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
            <thead class="">
                <tr>
                    <?php if(isset($_GET["transaction_id"])){?>
                        <th>Action</th>
                    <?php } ?>
                    <th>No.</th>
                    <th>Store</th>
                    <th>Grade</th>
                    <th>Member No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $member_id=$this->request->getGet("member_id");
                $builder = $this->db
                ->table("member")
                ->join("positionm", "positionm.positionm_id=member.positionm_id", "left")
                ->join("store", "store.store_id=member.store_id", "left")
                ->where("member.store_id", session()->get("store_id"));
                
                if(isset($_GET["member_no"])){
                        $builder->like("member_no",$this->request->getGet("member_no"),"BOTH");
                }

                if(isset($_GET["member_name"])){
                        $builder->like("member_name",$this->request->getGet("member_name"),"BOTH");
                }

                $usr=$builder->orderBy("member_id", "desc")
                    ->get();
                // echo $this->db->getLastquery();
                $no = 1;
                foreach ($usr->getResult() as $usr) { ?>
                    <tr>
                        <?php if(isset($_GET["transaction_id"])&&$_GET["transaction_id"]>0){
                        $transaction_id=$this->request->getGet("transaction_id");
                        ?>
                        <td style="padding-left:0px; padding-right:0px;">
                            <form method="post" class="btn-action" style="">
                                <button type="button" class="btn btn-sm btn-success" onclick="insertmember(<?= $transaction_id; ?>,<?= $usr->member_id; ?>,'<?= $usr->member_name; ?>')" ><span class="fa fa-check" style="color:white;"></span> </button>
                            </form>
                        </td>
                        <?php }?>
                        <td><?= $no++; ?></td>
                        <td><?= $usr->store_name; ?></td>
                        <td><?= $usr->positionm_name; ?></td>
                        <td><?= $usr->member_no; ?></td>
                        <td><?= $usr->member_name; ?></td>
                        <td><?= $usr->member_email; ?></td>
                        <td><?= $usr->member_address; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
       <?php
    }

    public function insertmember(){
        $input["member_id"]=$this->request->getGet("member_id");
        $where["transaction_id"]=$this->request->getGet("transaction_id");
        $this->db->table("transaction")
        ->update($input,$where);
        // echo $this->db->getLastQuery();
        echo $where["transaction_id"];

        
        $input1["customer_name"]=$this->request->getGet("customer_name");
        $where1["transaction_id"]=$this->request->getGet("transaction_id");
        $this->db->table("product")
        ->update($input1,$where1);
    }

    public function cekproductlanjutan(){
        $transaction_id=$this->request->getGet("transaction_id");
        $produk=array();
        $lanjutan=array();
        $durasi=array();
        $parent=array();
        $totaldurasi = 0;
        $alert = array();
        $start = array();
        $pchildren = array();
        $delparent = array();
        $transactiond = array();
        
        $product=$this->db->table("product")
        ->where("product_lanjutan>0")
        ->get();
        foreach($product->getResult() as $xproduct){
            $lanjutan[]=$xproduct->product_lanjutan;
        }
        $stransactiond=$this->db->table("transactiond")
        ->join("product","product.product_id=transactiond.product_id","left")
        ->where("transactiond.transaction_id",$transaction_id)
        ->orderBy("product_lanjutan","ASC")
        ->get();
        foreach($stransactiond->getResult() as $xtransactiond){
            $produk[]=$xtransactiond->product_id;
            $transactiond[$xtransactiond->product_id]=$xtransactiond->transactiond_id;
            if($xtransactiond->product_durasi>0){
                if($xtransactiond->product_lanjutan>0){
                    $durasi[$xtransactiond->product_lanjutan][]=$xtransactiond->product_durasi;
                    $alert[$xtransactiond->product_lanjutan] = $xtransactiond->product_dbend;
                    $pchildren[]=$xtransactiond->product_lanjutan;
                }else{
                    $durasi[$xtransactiond->product_id][]=$xtransactiond->product_durasi;
                    $parent[]=$xtransactiond->product_id;
                    $alert[$xtransactiond->product_id] = $xtransactiond->product_dbend;
                    $start[$xtransactiond->product_id] = $xtransactiond->transactiond_start;
                    $delparent[$xtransactiond->product_id]=$xtransactiond->transactiond_id;
                }
            }
        }
        // print_r($produk);
        // print_r($lanjutan);
        $random=chr(rand(97,122));
        $temp='
        <script>
        function '.$random.'(){
            $(".blanjutan").addClass("disabled");
            $(".blanjutan").removeAttr("onclick"); 
        ';
        foreach($produk as $xproduk){                
            if(in_array($xproduk,$lanjutan)){
                $temp.= '       
                setTimeout(function(){
                    let myStr = $(".ilanjutan'.$xproduk.'").val();
                    let strArray = myStr.split(",");
                    let isiilanjutan = "";
                    let koma = "";
                    let isil = 0;
                    for(let i = 0; i <= strArray.length; i++){
                        if(i==3){
                            isil=1;
                        }else{
                            isil=strArray[i];
                        }
                        if(i<strArray.length){
                            koma=",";
                        }else{
                            koma="";
                        }
                        isiilanjutan+=isil+koma;
                    }
                    $(".planjutan'.$xproduk.'").removeClass("disabled");
                    //$(".planjutan'.$xproduk.'").attr("onclick",isiilanjutan); 
                    $(".planjutan'.$xproduk.'").attr("onclick",$(".ilanjutan'.$xproduk.'").val()); 
                    // alert($(".ilanjutan'.$xproduk.'").val());   
                    // alert("ada='.$xproduk.'"); 
                    //alert(isiilanjutan);
                    
                },200);                            
                ';
            }
        }
        foreach($pchildren as $xpchildren){ 
            $temp.= '                 
                $("#del'. $delparent[$xpchildren].'").hide();       
            ';
        }
        $temp.='}        
        $("#'.$random.'").click();       
        </script>
        <input type="button" id="'.$random.'" onclick="'.$random.'()" class="hide"/>
        ';
        echo $temp;
        foreach($parent as $xparent){
            $totaldurasi = 0;
            $pid=$xparent;
            $totaldurasi =  array_sum($durasi[$pid]);
            $peringatan = $alert[$pid];
            $awal = $start[$pid];
            $akhir = date("Y-m-d H:i:s", strtotime($start[$pid]." + ".$totaldurasi." minute"));
            $ingat = date("Y-m-d H:i:s", strtotime($akhir." - ".$peringatan." minute"));
            $transactiondid = $transactiond[$pid];
             
            $input["product_start"]=$awal;
            $input["product_end"]=$akhir;
            $input["product_bend"]=$ingat;
            $input["transaction_id"]=$transaction_id;
            $where["product_id"]=$pid;
            $this->db->table("product")
            ->update($input,$where);
             
           /*  $input1["transactiond_start"]=$awal;
            $input1["transactiond_end"]=$akhir;
            $input1["transactiond_bend"]=$ingat;
            $where1["transactiond_id"]=$transactiondid;
            $this->db->table("transactiond")
            ->update($input1,$where1); */
        }
    }

    
}
