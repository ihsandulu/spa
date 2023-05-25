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
                                    isset(session()->get("halaman")['10']['act_create']) 
                                    && session()->get("halaman")['10']['act_create'] == "1"
                                )
                            ) { ?>
                            <form method="post" class="col-md-2">
                                <h1 class="page-header col-md-12">
                                    <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                    <input type="hidden" name="category_id" />
                                </h1>
                            </form>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Kategori";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Kategori";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">                                                     
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="category_name">Nama Kategori:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="category_name" name="category_name" placeholder="" value="<?= $category_name; ?>">
                                    </div>
                                </div>                               
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="category_lanjutan">Kategori lanjutan dari (opsional):</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $category = $this->db->table("category")
                                            ->where("store_id",session()->get("store_id"))
                                            ->orderBy("category_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="category_lanjutan" name="category_lanjutan">
                                            <option value="0" <?= ($category_lanjutan == "0") ? "selected" : ""; ?>>Pilih Kategori Induk</option>
                                            <?php
                                            foreach ($category->getResult() as $category) { ?>
                                                <option value="<?= $category->category_id; ?>" <?= ($category_lanjutan == $category->category_id) ? "selected" : ""; ?>><?= $category->category_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div> 
                                                             
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="position_id">Terkait dengan posisi (opsional):</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $position = $this->db->table("position")
                                            ->where("store_id",session()->get("store_id"))
                                            ->orderBy("position_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="position_id" name="position_id">
                                            <option value="0" <?= ($position_id == "0") ? "selected" : ""; ?>>Pilih Posisi</option>
                                            <?php
                                            foreach ($position->getResult() as $position) { ?>
                                                <option value="<?= $position->position_id; ?>" <?= ($position_id == $position->position_id) ? "selected" : ""; ?>><?= $position->position_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div> 
                                
                                <div class="form-group form-check">
                                    <label class="form-check-label pl-3">
                                    <input class="form-check-input" type="checkbox" id="category_unique" name="category_unique" value="1" <?=($category_unique==1)?"checked":"";?>> Unik Product
                                    </label>
                                </div>                                                   
                                

                                <input type="hidden" name="category_id" value="<?= $category_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("category"); ?>">Back</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
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
                                            <th>Action</th>
                                        <?php } ?>
                                        <th>No.</th>
                                        <th>Toko</th>
                                        <th>Unik</th>
                                        <th>Posisi</th>
                                        <th>Kategori</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $usr = $this->db
                                        ->table("category")
                                        ->join("position","position.position_id=category.position_id","left")
                                        ->join("(SELECT category_id AS pid, category_name AS pname FROM category)categorylanjutan", "categorylanjutan.pid=category.category_lanjutan", "left")
                                        ->join("store", "store.store_id=category.store_id", "left")
                                        ->where("category.store_id",session()->get("store_id"))
                                        ->orderBy("category_name", "ASC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) { 
                                        $unik=array("Tidak","Ya");
                                            ?>
                                        <tr>
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
                                                            isset(session()->get("halaman")['10']['act_update']) 
                                                            && session()->get("halaman")['10']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                        <input type="hidden" name="category_id" value="<?= $usr->category_id; ?>" />
                                                    </form>
                                                    <?php }?>
                                                    
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
                                                            isset(session()->get("halaman")['10']['act_delete']) 
                                                            && session()->get("halaman")['10']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                        <input type="hidden" name="category_id" value="<?= $usr->category_id; ?>" />
                                                    </form>
                                                    <?php }?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $unik[$usr->category_unique]; ?></td>
                                            <td><?= $usr->position_name; ?></td>
                                            <td>
                                                <?= $usr->category_name; ?>
                                                <?php if($usr->category_lanjutan>0){?>
                                                <br/><small class="text-danger">Lanjutan: <?= $usr->pname; ?></small>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "Master Kategori";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>