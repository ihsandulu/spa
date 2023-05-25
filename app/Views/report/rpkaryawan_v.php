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
                                        <th>Date</th>
                                        <th>Toko</th>
                                        <th>Posisi</th>
                                        <th>Nama</th>
                                        <th>Invoice</th>
                                        <th>Product</th>
                                        <th>Komisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                    ->table("transaction")
                                    ->join("store", "store.store_id=transaction.store_id", "left")
                                    ->join("transactiond", "transactiond.transaction_id=transaction.transaction_id", "left")
                                    ->join("user", "user.user_id=transactiond.user_id", "left")
                                    ->join("(SELECT user_id AS trainer_id, user_name AS trainer_name FROM user)trainer", "trainer.trainer_id=user.user_trainer", "left")
                                    ->join("(SELECT user_id AS sales_id, user_name AS sales_name FROM user)sales", "sales.sales_id=user.user_sales", "left")
                                    ->join("product", "product.product_id=transactiond.product_id", "left")
                                    ->where("transaction.store_id",session()->get("store_id"))
                                    ->where("transactiond.user_id > 0");
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
                                    $usr= $builder
                                        ->orderBy("transaction.transaction_id", "DESC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    $ttransactionnom=0;
                                    foreach ($usr->getResult() as $usr) { 
                                        for($x=100;$x<=102;$x++){
                                            switch($x){
                                                case 100:
                                                    $jabatan = "Therapist";
                                                    $nama = $usr->user_name;
                                                    $profit = $usr->product_profittherapist;
                                                break;
                                                case 101:
                                                    $jabatan = "Trainer";
                                                    $nama = $usr->trainer_name;
                                                    $profit = $usr->product_profittrainer;
                                                break;
                                                case 102:
                                                    $jabatan = "Sales Product";
                                                    $nama = $usr->sales_name;
                                                    $profit = $usr->product_profitsales;
                                                break;
                                            }
                                        ?>
                                        <tr>                                            
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->transaction_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $jabatan; ?></td>
                                            <td><?= $nama; ?></td>
                                            <td><?= $usr->transaction_no; ?></td>
                                            <td><?= $usr->product_name; ?></td>
                                            <td><?= number_format($profit,0,".",","); $ttransactionnom+=$profit; ?></td>
                                        </tr>
                                    <?php }
                                    } ?>
                                    
                                        <tr>
                                            <td><?= $no; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Total&nbsp;</td>
                                            <td><?= number_format($ttransactionnom,0,".",","); ?></td>
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
    var title = "Laporan Penghasilan Karyawan";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>