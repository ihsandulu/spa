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
                        
                        <form action="<?= base_url("rtransaction"); ?>" method="get" class="col-md-2">
                            <h1 class="page-header col-md-12">
                                <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                            </h1>
                        </form>
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
                                        <th>Toko</th>
                                        <th>Produk</th>
                                        <th>Nota Transaksi</th>
                                        <th>Qty</th>
                                        <th>Nominal</th>
                                        <th>Durasi</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("transactiond")
                                        ->select("
                                        *, 
                                        transactiond.transaction_id AS transaction_id
                                        ")
                                        ->join("transaction", "transaction.transaction_id=transactiond.transaction_id", "left")
                                        ->join("(SELECT user_id as uid, user_name as therapy, user_trainer, user_sales FROM user)user", "user.uid=transactiond.user_id", "left")
                                        ->join("(SELECT user_id as uid1, user_name as trainer FROM user)trainer", "trainer.uid1=user.user_trainer", "left")
                                        ->join("(SELECT user_id as uid2, user_name as sales FROM user)sales", "sales.uid2=user.user_sales", "left")
                                        ->join("store", "store.store_id=transactiond.store_id", "left")
                                        ->join("product", "product.product_id=transactiond.product_id", "left")
                                        ->where("transactiond.store_id",session()->get("store_id"));
                                        // ->where("transaction.transaction_status","0");
                                    if(isset($_GET["from"])&&$_GET["from"]!=""){
                                        $builder->where("transaction_date >=",$this->request->getGet("from"));
                                    }else{
                                        $builder->where("transaction_date",date("Y-m-d"));
                                    }
                                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                                        $builder->where("transaction_date <=",$this->request->getGet("to"));
                                    }else{
                                        $builder->where("transaction_date",date("Y-m-d"));
                                    }
                                    $usr= $builder
                                        ->orderBy("product_name", "ASC")
                                        ->get();
                                    // echo $this->db->getLastquery();
                                    $no = 1;
                                    $test="";
                                    foreach ($usr->getResult() as $usr) {    
                                        $test="";   
                                        $upah_therapist=$usr->transactiond_profittherapist*$usr->transactiond_qty;
                                        $upah_trainer=$usr->transactiond_profittrainer*$usr->transactiond_qty;
                                        $upah_sales=$usr->transactiond_profitsales*$usr->transactiond_qty;     

                                        if($usr->product_lanjutan>0 && $usr->product_durasi>0){
                                            $lanj=$this->db->table("transactiond")
                                            ->join("(SELECT user_id as uid, user_name as therapy, user_trainer, user_sales FROM user)user", "user.uid=transactiond.user_id", "left")
                                            ->join("(SELECT user_id as uid1, user_name as trainer FROM user)trainer", "trainer.uid1=user.user_trainer", "left")
                                            ->join("(SELECT user_id as uid2, user_name as sales FROM user)sales", "sales.uid2=user.user_sales", "left")
                                            ->where("transaction_id",$usr->transaction_id)
                                            ->where("product_id",$usr->product_lanjutan)
                                            ->get();
                                            $test=$this->db->getLastquery();
                                            foreach($lanj->getResult() as $lanj){
                                                $usr->user_id=$lanj->user_id;
                                                $usr->therapy=$lanj->therapy;
                                                $usr->trainer=$lanj->trainer;
                                                $usr->sales=$lanj->sales;
                                                $upah_therapist=$lanj->transactiond_profittherapist*$usr->transactiond_qty;
                                                $upah_trainer=$lanj->transactiond_profittrainer*$usr->transactiond_qty;
                                                $upah_sales=$lanj->transactiond_profitsales*$usr->transactiond_qty; 
                                                $test=$lanj->transactiond_qty; 
                                            }
                                        }
                                        ?>
                                        <tr>                                            
                                            <td><?= $no++; ?><?php //echo $test;?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td class="text-left">
                                                <?= $usr->product_name; ?>
                                            </td>
                                            <td><?= $usr->transaction_no; ?></td>
                                            <td class="text-right"><?= number_format($usr->transactiond_qty,0,".",","); ?></td>
                                            <td class="text-right"><?= number_format($usr->transactiond_price,0,".",","); ?></td>
                                            <td class="text-center">
                                                <?php if($usr->transactiond_start!="0000-00-00 00:00:00"){?>
                                                    <div class="row">
                                                        <div class="col-4 text-left">From:</div>
                                                        <div class="col-8 text-left text-secondary"><?=$usr->transactiond_start;?></div>
                                                        <div class="col-4 text-left">To:</div>
                                                        <div class="col-8 text-left text-secondary"><?=$usr->transactiond_end;?></div>
                                                    </div>
                                                <?php }?>
                                            </td>
                                            <td class="text-left">
                                                <?php if($usr->user_id!="0"){?>
                                                    <div class="row">
                                                        <div class="col-4 text-left">Therapy:</div>
                                                        <div class="col-8 text-left text-secondary"><?= $usr->therapy; ?> (<?= number_format($upah_therapist,0,".",","); ?>)</div>
                                                        <div class="col-4 text-left">Trainer:</div>
                                                        <div class="col-8 text-left text-secondary"><?= $usr->trainer; ?> (<?= number_format($upah_trainer,0,".",","); ?>)</div>
                                                        <div class="col-4 text-left">Sales:</div>
                                                        <div class="col-8 text-left text-secondary"><?= $usr->sales; ?> (<?= number_format($upah_sales,0,".",","); ?>)</div> 
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
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
    var title = "Laporan Transaksi";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>