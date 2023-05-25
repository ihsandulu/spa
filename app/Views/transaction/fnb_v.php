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
                       
                        <?php if (!isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['report'])) { ?>
                            <?php if (isset($_GET["user_id"])) { ?>
                                <form action="<?= site_url("user"); ?>" method="get" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                    </h1>
                                </form>
                            <?php } ?>
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
                                    isset(session()->get("halaman")['32']['act_create']) 
                                    && session()->get("halaman")['32']['act_create'] == "1"
                                )
                            ) { ?>
                            <form method="post" class="col-md-2">
                                <h1 class="page-header col-md-12">
                                    <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                    <input type="hidden" name="fnb_id" />
                                </h1>
                            </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update F&B";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah F&B";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">     
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="fnb_date">Tgl:</label>
                                    <div class="col-sm-10">
                                        <input type="date" autofocus class="form-control" id="fnb_date" name="fnb_date" placeholder="" value="<?= $fnb_date; ?>">
                                    </div>
                                </div>    
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="fnb_name">Keperluan:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="fnb_name" name="fnb_name" placeholder="Isi Keperluan atau Nama Barang" value="<?= $fnb_name; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="unit_id">Unit:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $unit = $this->db->table("unit")
                                            ->where("store_id",session()->get("store_id"))
                                            ->orderBy("unit_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select class="form-control select" id="unit_id" name="unit_id">
                                            <option value="0" <?= ($unit_id == "0") ? "selected" : ""; ?>>Pilih unit</option>
                                            <?php
                                            foreach ($unit->getResult() as $unit) { ?>
                                                <option value="<?= $unit->unit_id; ?>" <?= ($unit_id == $unit->unit_id) ? "selected" : ""; ?>><?= $unit->unit_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>      
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="fnb_qty">Qty:</label>
                                    <div class="col-sm-12">
                                        <input type="number" autofocus class="form-control" id="fnb_qty" name="fnb_qty" placeholder="" value="<?= $fnb_qty; ?>">
                                    </div>
                                </div>       
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="fnb_price">Nominal:</label>
                                    <div class="col-sm-12">
                                        <input type="number" autofocus class="form-control" id="fnb_price" name="fnb_price" placeholder="" value="<?= $fnb_price; ?>">
                                    </div>
                                </div>                                
                                

                                <input type="hidden" name="fnb_id" value="<?= $fnb_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("fnb"); ?>">Back</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
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
                            <?php if(isset($_GET["report"])){?>                                
                            <input type="hidden" id="report" name="report" class="form-control" value="<?=$this->request->getGet("report");?>">&nbsp;
                            <?php }?>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong>
                            </div>
                        <?php } ?>

                        <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <?php if (!isset($_GET["report"])) { ?>
                                        <th>Aksi.</th>
                                        <?php }?>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>Toko</th>
                                        <th>Keperluan</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $currentURL = current_url();
                                    $currentURL = str_replace('/index.php', '', $currentURL);
                                    $params   = $_SERVER['QUERY_STRING'];
                                    $fullURL = urlencode($currentURL . '?' . $params);
                                    $builder = $this->db
                                        ->table("fnb")
                                        ->join("unit", "unit.unit_id=fnb.unit_id", "left")
                                        ->join("store", "store.store_id=fnb.store_id", "left")
                                        ->join("user", "user.user_id=fnb.user_id", "left")
                                        ->where("fnb.store_id",session()->get("store_id"));
                                    if(isset($_GET["from"])&&$_GET["from"]!=""){
                                        $builder->where("fnb.fnb_date >=",$this->request->getGet("from"));
                                    }else{
                                        $builder->where("fnb.fnb_date",date("Y-m-d"));
                                    }

                                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                                        $builder->where("fnb.fnb_date <=",$this->request->getGet("to"));
                                    }else{
                                        $builder->where("fnb.fnb_date",date("Y-m-d"));
                                    }
                                    $usr= $builder
                                        ->orderBy("fnb.fnb_id", "ASC")
                                        ->get();
                                    // echo $this->db->getLastquery();die;
                                    $no = 1;
                                    $thargasetelahppn=0;
                                    $tnominal=0;
                                    foreach ($usr->getResult() as $usr) { 
                                        ?>
                                        <tr>    
                                            <td style="padding-left:0px; padding-right:0px;">                                                    
                                                
                                                
                                                <?php if (!isset($_GET["report"])) { ?>
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
                                                        isset(session()->get("halaman")['32']['act_update']) 
                                                        && session()->get("halaman")['32']['act_update'] == "1"
                                                    )
                                                ) { ?>
                                                <form method="post" class="btn-action" style="">
                                                    <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                    <input type="hidden" name="fnb_id" value="<?= $usr->fnb_id; ?>" />
                                                </form>
                                                <?php }?>
                                                <?php }?>
                                                
                                                <?php if (!isset($_GET["report"])) { ?>                                                    
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
                                                        isset(session()->get("halaman")['32']['act_delete']) 
                                                        && session()->get("halaman")['32']['act_delete'] == "1"
                                                    )
                                                ) { ?>
                                                <form method="post" class="btn-action" style="">
                                                    <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                    <input type="hidden" name="fnb_id" value="<?= $usr->fnb_id; ?>" />
                                                </form>
                                                <?php }?>
                                                <?php } ?>
                                            </td>
                                            <td><?= $no++; ?></td>                                                  
                                            <td><?= $usr->fnb_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->fnb_name; ?></td>
                                            <td><?= number_format($usr->fnb_qty,0,",","."); ?> <?= $usr->unit_name; ?></td>
                                            <td><?= number_format($usr->fnb_price,0,",","."); $tnominal+=$usr->fnb_price; ?></td>
                                        </tr>
                                    <?php } ?>
                                    
                                    <tr>
                                        <?php if (!isset($_GET["report"])) { ?>
                                        <td></td>
                                        <?php }?>
                                        <td><?= $no++; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total&nbsp;</td>
                                        <td><?= number_format($tnominal,0,".",","); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                        
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "<?=(isset($_GET["report"]))?"Laporan":"";?> F&B";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>