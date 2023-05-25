<?php echo $this->include("template/header_v"); ?>

<div class='container-fluid'>
    <div class='row'>
        <div class='col-12'>
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <?php if (!isset($_GET['user_id']) && !isset($_POST['new']) && !isset($_POST['edit'])) {
                            $coltitle = "col-md-10";
                        } else {
                            $coltitle = "col-md-8";
                        } ?>
                        <div class="<?= $coltitle; ?>">
                            <h4 class="card-title"></h4>
                            <!-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> -->
                        </div>
                    </div>
                    <?php 
                    if(isset($_GET["from"])&&$_GET["from"]!=""){
                        $from=$_GET["from"];
                    }else{
                        $from=date("Y-m-d");
                    }

                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                        $to=$_GET["to"];
                    }else{
                        $to=date("Y-m-d");
                    }

                    ?>
                    <form class="form-inline" >
                        <label for="from">Dari:</label>&nbsp;
                        <input type="date" id="from" name="from" class="form-control" value="<?=$from;?>">&nbsp;
                        <label for="to">Ke:</label>&nbsp;
                        <input type="date" id="to" name="to" class="form-control" value="<?=$to;?>">&nbsp;
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>

                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong>
                            </div>
                        <?php } ?>

                        <div class="table-responsive m-t-40">
                            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <th>No.</th>
                                        <th>Kategori</th>
                                        <th>Room Package</th>
                                        <th>Qty</th>
                                        <th>Jml Tamu</th>
                                        <th>Room</th>
                                        <th>Therapist</th>
                                        <th>Trainer</th>
                                        <th>Sales</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                    ->table("product")
                                    ->join("store", "store.store_id=product.store_id", "left")                                              
                                    ->where("product.store_id",session()->get("store_id"))
                                    ->where("product.category_id","100");
                                    /* if(isset($_GET["from"])&&$_GET["from"]!=""){
                                        $builder->where("transaction.transaction_date >=",$this->request->getGet("from"));
                                    }else{
                                        $builder->where("transaction.transaction_date",date("Y-m-d"));
                                    }
                                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                                        $builder->where("transaction.transaction_date <=",$this->request->getGet("to"));
                                    }else{
                                        $builder->where("transaction.transaction_date",date("Y-m-d"));
                                    } */
                                    $usr= $builder
                                        ->orderBy("product.product_standard", "ASC")
                                        ->orderBy("product.product_name", "ASC")
                                        ->get();
                                    // echo $this->db->getLastquery();
                                    $no = 1;
                                    $qty=0;
                                    $tamu=0;
                                    $room=0;
                                    $total_therapist = 0;
                                    $total_trainer = 0;
                                    $total_sales = 0;
                                    $tqty=0;
                                    $ttamu=0;
                                    $troom=0;
                                    $total_ttherapist=0;
                                    $total_ttrainer=0;
                                    $total_tsales=0;
                                    $price=0;
                                    $total_price=0;
                                    foreach ($usr->getResult() as $usr) {    
                                        //$qty   
                                        $builder=$this->db->table("transactiond")
                                        ->select("COUNT(product_id)AS qty, product_id, transactiond.transaction_id AS transaction_id")
                                        ->join("transaction","transaction.transaction_id=transactiond.transaction_id","left")
                                        ->where("transactiond.product_id",$usr->product_id);
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
                                        $transactiond=$builder->groupBy("product_id")
                                        ->get(); 
                                        $qty=0;
                                        // $tamu=0;
                                        foreach($transactiond->getResult() as $transactiond){
                                            $qty=$transactiond->qty;
                                            // $tamu=$transactiond->qty;
                                        } 
                                        
                                        //$tamu   
                                        $builder=$this->db->table("transactiond")
                                        ->select("COUNT(user_id)AS tamu, product_id, transactiond.transaction_id AS transaction_id")
                                        ->join("transaction","transaction.transaction_id=transactiond.transaction_id","left")
                                        ->where("transactiond.product_id",$usr->product_id);
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
                                        $transactiond=$builder->groupBy("user_id")
                                        ->get(); 
                                        // $qty=0;
                                        $tamu=0;
                                        foreach($transactiond->getResult() as $transactiond){
                                            // $qty=$transactiond->qty;
                                            $tamu=$transactiond->tamu;
                                        } 
                                        
                                         
                                        
                                        
                                        //$therapist   
                                        $builder=$this->db->table("transactiond")
                                        ->select("transactiond_qty,
                                        transactiond_lanjutan AS tlanjutan, 
                                        SUM(transactiond_price) AS price, 
                                        SUM(transactiond_profittherapist*transactiond_qty) AS ttherapist, 
                                        SUM(transactiond_profittrainer*transactiond_qty) AS ttrainer,
                                        SUM(transactiond_profitsales*transactiond_qty) AS tsales, 
                                        transactiond.product_id,
                                        transactiond.transactiond_price
                                        ")
                                        ->join("transaction","transaction.transaction_id=transactiond.transaction_id","left")                                        
                                        ->where("transactiond.product_id",$usr->product_id);
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
                                        $transactiond=$builder->groupBy("transactiond.product_id")
                                        ->get(); 
                                        $therapist=0;
                                        $ttherapist=0;
                                        $ttrainer=0;
                                        $tsales=0;
                                        $price = 0;
                                        // echo $this->db->getLastquery();
                                        foreach($transactiond->getResult() as $transactiond){
                                            $ttherapist=$transactiond->ttherapist;
                                            $ttrainer=$transactiond->ttrainer;
                                            $tsales=$transactiond->tsales;
                                            $price = $transactiond->price;
                                        }  
                                        
                                        //$therapist  lanjutan 
                                        $builder=$this->db->table("transactiond")
                                        ->select("transactiond_qty,
                                        transactiond_lanjutan AS tlanjutan, 
                                        SUM(transactiond_price) AS price, 
                                        SUM(transactiond_profittherapist*transactiond_qty) AS ttherapist, 
                                        SUM(transactiond_profittrainer*transactiond_qty) AS ttrainer,
                                        SUM(transactiond_profitsales*transactiond_qty) AS tsales, 
                                        transactiond.product_id,
                                        transactiond.transactiond_price
                                        ")
                                        ->join("transaction","transaction.transaction_id=transactiond.transaction_id","left")                                        
                                        ->where("transactiond.transactiond_lanjutan",$usr->product_id);
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
                                        $transactiond=$builder->groupBy("transactiond.transactiond_lanjutan")
                                        ->get();                                       
                                        $ttherapist1=0;
                                        $ttrainer1=0;
                                        $tsales1=0;
                                        $price1 = 0;
                                        // echo $this->db->getLastquery();die;
                                        foreach($transactiond->getResult() as $transactiond){
                                            $ttherapist1=$transactiond->ttherapist;
                                            $ttrainer1=$transactiond->ttrainer;
                                            $tsales1=$transactiond->tsales;
                                            $price1 = $transactiond->price;
                                        } 
                                        $total_therapist=$ttherapist+$ttherapist1;
                                        $total_trainer=$ttrainer+$ttrainer1;
                                        $total_sales=$tsales+$tsales1;
                                        $price = $price+$price1;

                                        $room = $price-$total_therapist-$total_trainer-$total_sales;
                                    ?>
                                        <tr>                                            
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->product_standard; ?></td>
                                            <td><?= $usr->product_name; ?></td>
                                            <td><?= number_format($qty,0,".",","); $tqty+=$qty; ?></td>
                                            <td><?= number_format($tamu,0,".",","); $ttamu+=$tamu; ?></td>
                                            <td><?= number_format($room,0,".",","); $troom+=$room; ?></td>
                                            <td><?= number_format($total_therapist,0,".",","); $total_ttherapist+=$total_therapist; ?></td>
                                            <td><?= number_format($total_trainer,0,".",","); $total_ttrainer+=$total_trainer; ?></td>
                                            <td><?= number_format($total_sales,0,".",","); $total_tsales+=$total_sales; ?></td>
                                            <td><?= number_format($price,0,".",","); $total_price+=$price; ?></td>
                                        </tr>
                                    <?php }
                                    ?>
                                    
                                        <tr>
                                            <td><?= $no; ?></td>
                                            <td></td>
                                            <td class="text-right">Total&nbsp;</td>
                                            <td><?= number_format($tqty,0,".",","); ?></td>
                                            <td><?= number_format($ttamu,0,".",","); ?></td>
                                            <td><?= number_format($troom,0,".",","); ?></td>
                                            <td><?= number_format($total_ttherapist,0,".",","); ?></td>
                                            <td><?= number_format($total_ttrainer,0,".",","); ?></td>
                                            <td><?= number_format($total_tsales,0,".",","); ?></td>
                                            <td><?= number_format($total_price,0,".",","); ?></td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "Laporan Room";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>