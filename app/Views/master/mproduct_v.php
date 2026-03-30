<?php echo $this->include("template/header_v"); ?>
<?php if ($page == "room") {
    $title = "Room";
} else
if ($page == "paket") {
    $title = "Paket";
} else
if ($page == "loker") {
    $title = "Loker";
} else {
    $title = "Produk";
} ?>

<style>
    .lproductb {
        line-height: 1.2;
        font-size: 9px;
        margin-bottom: 0px;
        padding: 2px;
        background-color: rgba(119, 153, 231, 0.6);
    }
</style>

<div class='container-fluid'>
    <div class='row'>
        <div class='col-12'>
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <?php if (!isset($_GET['user_id']) && !isset($_POST['new']) && !isset($_POST['edit'])) {
                            $coltitle = "col-md-6";
                        } else {
                            $coltitle = "col-md-4";
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
                                    isset(session()->get("halaman")['8']['act_create'])
                                    && session()->get("halaman")['8']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-4">
                                    <h1 class="page-header col-md-12">
                                        <button name="updatebuy" class="btn btn-warning btn-block btn-lg" value="OK" style="">Update Harga Beli</button>
                                    </h1>
                                </form>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="product_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update " . $title;
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah " . $title;
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <?php if ($page == "room" || $page == "loker" || $page == "paket") { ?>
                                    <input type="hidden" id="product_type" name="product_type" value="1" />
                                <?php } else { ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="product_type">Tipe:</label>
                                        <div class="col-sm-10">
                                            <select onchange="tipe();durasi();lanjutan();posisi();" required class="form-control" id="product_type" name="product_type">
                                                <option value="" <?= ($product_type == "") ? "selected" : ""; ?>>Pilih Type</option>
                                                <option value="0" <?= ($product_type == "0") ? "selected" : ""; ?>>Barang</option>
                                                <option value="1" <?= ($product_type == "1") ? "selected" : ""; ?>>Jasa</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <script>
                                    function tipe() {
                                        let atipe = $("#product_type").val();
                                        if (atipe == 1) {
                                            setTimeout(() => {
                                                $(".barang").hide();
                                                $("#jual").text("Harga");
                                            }, 500);
                                        } else {
                                            $(".barang").show();
                                            $("#jual").text("Jual");
                                        }
                                    }
                                    tipe();
                                </script>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="category_id">Kategori:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $builder = $this->db->table("category")
                                            ->join("position", "position.position_id=category.position_id", "left")
                                            ->where("category.store_id", session()->get("store_id"));
                                        if ($page == "room") {
                                            $builder->like("category_name", "room", "both");
                                        } else
                                        if ($page == "loker") {
                                            $builder->like("category_name", "loker", "both");
                                        } else
                                        if ($page == "paket") {
                                            $builder->like("category_name", "paket", "both");
                                        } else {
                                            $builder->notLike("category_name", "loker", "both");
                                            $builder->notLike("category_name", "room", "both");
                                            $builder->notLike("category_name", "paket", "both");
                                        }
                                        $category = $builder
                                            ->orderBy("category_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select onchange="durasi();lanjutan();urutan();posisi();" required class="form-control select" id="category_id" name="category_id">
                                            <option value="0" <?= ($category_id == "0") ? "selected" : ""; ?>>Pilih Kategori</option>
                                            <?php
                                            $firstword = "";
                                            foreach ($category->getResult() as $category) { ?>
                                                <option
                                                    <?php
                                                    if ($category->position_id > 0) {
                                                        $myvalue = $category->position_name;
                                                        $arr = explode(' ', trim($myvalue));
                                                        $firstword = strtolower($arr[0]);
                                                    }
                                                    ?>
                                                    posisi="<?= $firstword; ?>"
                                                    durasi="<?= $category->category_durasi; ?>"
                                                    lanjutan="<?= $category->category_lanjutan; ?>"
                                                    unique="<?= $category->category_unique; ?>"
                                                    value="<?= $category->category_id; ?>"
                                                    <?= ($category_id == $category->category_id) ? "selected" : ""; ?>  <?= ($page == "paket" || $page == "loker") ? "selected" : ""; ?>>
                                                    <?= $category->category_name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <script>
                                            function durasi() {
                                                // let jasa=$("#product_type").val();
                                                let adurasi = $("#category_id>option:selected").attr("durasi");
                                                if (adurasi > 0) {
                                                    $(".durasi").show();
                                                    setTimeout(() => {
                                                        $(".pdurasi").addAttr("required");
                                                    }, 100);
                                                } else {
                                                    $(".durasi").hide();
                                                    $(".pdurasi").val("");
                                                    $(".pdurasi").removeAttr("required");
                                                }
                                            }

                                            function lanjutan() {
                                                let alanjutan = $("#category_id>option:selected").attr("lanjutan");
                                                if (alanjutan > 0) {
                                                    $(".lanjutan").show();
                                                    setTimeout(() => {
                                                        $(".planjutan").addAttr("required");
                                                    }, 100);
                                                } else {
                                                    $(".lanjutan").hide();
                                                    $(".planjutan").val("");
                                                    $(".planjutan").removeAttr("required");
                                                }
                                            }

                                            function urutan() {
                                                let category_id = $("#category_id").val();
                                                $.get("<?= base_url("urutan"); ?>", {
                                                        category_id: category_id
                                                    })
                                                    .done(function(data) {
                                                        $("#product_urutan").val(data);
                                                    });
                                            }

                                            function posisi() {
                                                $(".posisi").hide();
                                                $(".pposisi").removeAttr("required");
                                                let aposisi = $("#category_id>option:selected").attr("posisi");
                                                if (aposisi != "" && aposisi != undefined) {
                                                    // alert(aposisi);
                                                    <?php
                                                    if ($firstword != "") { ?>
                                                        $(".posisi").show();
                                                        setTimeout(() => {
                                                            $(".pposisi").addAttr("required");
                                                        }, 100);
                                                    <?php } else { ?>
                                                        $(".posisi" + aposisi).show();
                                                        setTimeout(() => {
                                                            $(".pposisi" + aposisi).addAttr("required");
                                                        }, 100);
                                                    <?php } ?>
                                                } else {
                                                    $(".posisi").hide();
                                                    $(".pposisi").val("");
                                                    $(".pposisi").removeAttr("required");
                                                }
                                            }
                                            setTimeout(() => {
                                                durasi();
                                                lanjutan();
                                                posisi();
                                                <?php if ($product_urutan == 0) { ?>
                                                    urutan();
                                                <?php } ?>
                                            }, 500);
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group durasi">
                                    <label class="control-label col-sm-12" for="product_standard">Standard/VIP:</label>
                                    <div class="col-sm-10">
                                        <select required class="form-control select pdurasi" id="product_standard" name="product_standard">
                                            <option value="" <?= ($product_standard == "") ? "selected" : ""; ?>>Pilih Standard/VIP</option>
                                            <option value="Standard" <?= ($product_standard == "Standard") ? "selected" : ""; ?>>Standard</option>
                                            <option value="VIP" <?= ($product_standard == "VIP") ? "selected" : ""; ?>>VIP</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group durasi">
                                    <label class="control-label col-sm-12" for="product_durasi">Durasi(menit):</label>
                                    <div class="col-sm-10">
                                        <input required type="number" autofocus class="form-control pdurasi" id="product_durasi" name="product_durasi" placeholder="" value="<?= $product_durasi; ?>">
                                    </div>
                                </div>
                                <div class="form-group durasi">
                                    <label class="control-label col-sm-12" for="product_dbend">Alert Before End(menit):</label>
                                    <div class="col-sm-10">
                                        <input required type="number" autofocus class="form-control pdurasi" id="product_dbend" name="product_dbend" placeholder="" value="<?= $product_dbend; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_name">Nama Produk:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="product_name" name="product_name" placeholder="" value="<?= $product_name; ?>">
                                    </div>
                                </div>
                                <?php if($page == "paket"){?>
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="product_roomqty">Jumlah Room:</label>
                                    <div class="col-sm-10">
                                        <select required class="form-control select pdurasi" id="product_roomqty" name="product_roomqty">
                                            <option value="" <?= ($product_roomqty == "") ? "selected" : ""; ?>>Pilih Jumlah Room</option>
                                            <option value="1" <?= ($product_roomqty == "1") ? "selected" : ""; ?>>1</option>
                                            <option value="2" <?= ($product_roomqty == "2") ? "selected" : ""; ?>>2</option>
                                            <option value="3" <?= ($product_roomqty == "3") ? "selected" : ""; ?>>3</option>
                                        </select>
                                    </div>
                                </div>
                                <?php }?>
                                <div class="form-group lanjutan">
                                    <label class="control-label col-sm-12" for="product_lanjutan">Produk lanjutan dari:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $product = $this->db->table("product")
                                            ->where("product_id !=", $product_id)
                                            ->where("category_id", "100")
                                            ->orderBy("category_id", "ASC")
                                            ->orderBy("product_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select planjutan" id="product_lanjutan" name="product_lanjutan">
                                            <option value="0" <?= ($product_lanjutan == "0") ? "selected" : ""; ?>>Pilih Product Induk</option>
                                            <?php
                                            foreach ($product->getResult() as $product) { ?>
                                                <option value="<?= $product->product_id; ?>" <?= ($product_lanjutan == $product->product_id) ? "selected" : ""; ?>><?= $product->product_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="unit_id">Unit:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $builder = $this->db->table("unit")
                                            ->where("store_id", session()->get("store_id"));
                                        if ($page == "room") {
                                            $builder->like("unit_name", "unit", "both");
                                        } else 
                                        if ($page == "paket") {
                                            $builder->like("unit_name", "unit", "both");
                                        } else
                                        if ($page == "loker") {
                                            $builder->like("unit_name", "unit", "both");
                                        } else {
                                        }
                                        $unit = $builder
                                            ->orderBy("unit_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="unit_id" name="unit_id">
                                            <option value="0" <?= ($unit_id == "0") ? "selected" : ""; ?>>Pilih Unit</option>
                                            <?php
                                            foreach ($unit->getResult() as $unit) { ?>
                                                <option value="<?= $unit->unit_id; ?>" <?= ($unit_id == $unit->unit_id) ? "selected" : ""; ?>><?= $unit->unit_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_description">Deskripsi:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="product_description" name="product_description" placeholder="" value="<?= $product_description; ?>">
                                    </div>
                                </div>
                                <div class="form-group barang">
                                    <label class="control-label col-sm-2" for="product_countlimit">Limit Stok:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control" id="product_countlimit" name="product_countlimit" placeholder="" value="<?= $product_countlimit; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_countlimit"));
                                    </script>
                                </div>
                                <div class="form-group barang">
                                    <label class="control-label col-sm-2" for="product_stock">Stok Real Time:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control" id="product_stock" name="product_stock" placeholder="" value="<?= $product_stock; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_stock"));
                                    </script>
                                </div>
                                <div class="form-group barang">
                                    <label class="control-label col-sm-2" for="product_buy">Beli:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control" id="product_buy" name="product_buy" placeholder="" value="<?= $product_buy; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_buy"));
                                    </script>
                                </div>
                                <div class="form-group">
                                    <label id="jual" class="control-label col-sm-2" for="product_sell">Jual:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control" id="product_sell" name="product_sell" placeholder="" value="<?= $product_sell; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_sell"));
                                    </script>
                                </div>
                                <div class="form-group posisi posisitherapist">
                                    <label id="jual" class="control-label col-sm-2" for="product_profittherapist">Profit Therapist:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control pposisi pposisitherapist" id="product_profittherapist" name="product_profittherapist" placeholder="" value="<?= $product_profittherapist; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_profittherapist"));
                                    </script>
                                </div>
                                <div class="form-group posisi posisitrainer">
                                    <label id="jual" class="control-label col-sm-2" for="product_profittrainer">Profit Trainer:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control pposisi pposisitrainer" id="product_profittrainer" name="product_profittrainer" placeholder="" value="<?= $product_profittrainer; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_profittrainer"));
                                    </script>
                                </div>
                                <div class="form-group posisi posisisales">
                                    <label id="jual" class="control-label col-sm-2" for="product_profitsales">Profit Sales Product:</label>
                                    <div class="col-sm-10">
                                        <input onkeyup="rupiahnumerik(this);" type="number" autofocus class="form-control pposisi pposisisales" id="product_profitsales" name="product_profitsales" placeholder="" value="<?= $product_profitsales; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#product_profitsales"));
                                    </script>
                                </div>
                                <?php if ($page == "produk") { ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="product_ube">UBE No.:</label>
                                        <div class="col-sm-10">
                                            <input type="text" autofocus class="form-control" id="product_ube" name="product_ube" placeholder="" value="<?= $product_ube; ?>">
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_urutan">Urutan:</label>
                                    <div class="col-sm-10">
                                        <input type="number" autofocus class="form-control" id="product_urutan" name="product_urutan" placeholder="" value="<?= $product_urutan; ?>">
                                    </div>
                                </div>

                                <div class="form-group form-check">
                                    <label class="form-check-label pl-3">
                                        <input class="form-check-input" type="checkbox" id="product_onoff" name="product_onoff" value="1" <?= ($product_onoff == 1 || $product_onoff == null) ? "checked" : ""; ?>> Product is On
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_picture">Photo Produk:</label>
                                    <div class="col-sm-10">
                                        <input type="file" autofocus class="form-control" id="product_picture" name="product_picture" placeholder="" value="<?= $product_picture; ?>">
                                        <?php if ($product_picture != "" && $product_picture != "product.png") {
                                            $user_image = "images/product_picture/" . $product_picture;
                                        } else {
                                            $user_image = "images/product_picture/no_image.png";
                                        } ?>
                                        <img id="product_picture_image" width="100" height="100" src="<?= base_url($user_image); ?>" />
                                        <script>
                                            function readURL(input) {
                                                if (input.files && input.files[0]) {
                                                    var reader = new FileReader();

                                                    reader.onload = function(e) {
                                                        $('#product_picture_image').attr('src', e.target.result);
                                                    }

                                                    reader.readAsDataURL(input.files[0]);
                                                }
                                            }

                                            $("#product_picture").change(function() {
                                                readURL(this);
                                            });
                                        </script>
                                    </div>
                                </div>

                                <input type="hidden" name="product_id" value="<?= $product_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button type="button" class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href='<?= base_url(uri_string()); ?>';">Back</button>
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
                        <style>
                            .on>td {
                                background: none;
                                color: black !important;
                            }

                            .off>td {
                                background: grey;
                                color: white !important;
                            }
                        </style>
                        <div class="table-responsive m-t-40">
                            <div class="modal fade" id="modalProduct" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Pilih Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form id="formProduct">
                                                <input type="hidden" id="selected_product_id">

                                                <div class="mb-3">
                                                    <label>Bundling</label> <label id="selected_product_name">Paket</label>
                                                    <select class="form-control" id="product_select">
                                                        <option value="">-- Pilih Product --</option>
                                                        <?php
                                                        $builder = $this->db
                                                            ->table("product")
                                                            ->where("product.category_id !=", "107");

                                                        $usr = $builder
                                                            ->orderBy("product_name", "ASC")
                                                            ->get();
                                                        //echo $this->db->getLastquery();
                                                        $no = 1;
                                                        foreach ($usr->getResult() as $usr) {
                                                        ?>
                                                            <option value="<?= $usr->product_id ?>"><?= $usr->product_name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="number" class="form-control mt-2" id="productb_qty" placeholder="Qty" value="1">
                                                </div>
                                            </form>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="button" class="btn btn-primary" id="submitProduct">Submit</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <table id="example23" class="display nowrap table table-hover  table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <?php if (!isset($_GET["report"])) { ?>
                                            <th>Action</th>
                                        <?php } ?>
                                        <th>No.</th>
                                        <th>Type</th>
                                        <th>Kategori</th>
                                        <th>Urutan</th>
                                        <th>Unit</th>
                                        <th>Produk</th>
                                        <?php if ($page == "produk") { ?>
                                            <th>Ube</th>
                                            <th>Limit</th>
                                            <th>Stok</th>
                                            <th>Status</th>
                                            <th>Beli</th>
                                        <?php } ?>
                                        <th>Jual</th>
                                        <th>Margin</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("product")
                                        ->join("(SELECT product_id AS pid, product_name AS pname FROM product)productlanjutan", "productlanjutan.pid=product.product_lanjutan", "left")
                                        ->join("category", "category.category_id=product.category_id", "left")
                                        ->join("unit", "unit.unit_id=product.unit_id", "left")
                                        ->join("store", "store.store_id=product.store_id", "left")
                                        ->where("product.store_id", session()->get("store_id"));
                                    if ($page == "room") {
                                        $builder->like("category.category_name", "room", "both");
                                    } else
                                    if ($page == "paket") {
                                        $builder->like("category.category_name", "paket", "both");
                                    } else
                                    if ($page == "loker") {
                                        $builder->like("category.category_name", "loker", "both");
                                    } else {
                                        $builder->notLike("category.category_name", "room", "both");
                                        $builder->notLike("category.category_name", "paket", "both");
                                        $builder->notLike("category.category_name", "loker", "both");
                                    }
                                    $usr = $builder
                                        ->orderBy("product_onoff", "DESC")
                                        ->orderBy("product.category_id", "ASC")
                                        ->orderBy("product_urutan", "ASC")
                                        ->orderBy("product_name", "ASC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) {
                                        $type = array("Barang", "Jasa");
                                        $onoff = array("off", "on");
                                    ?>
                                        <tr class="<?= $onoff[$usr->product_onoff]; ?>">
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
                                                            isset(session()->get("halaman")['8']['act_update'])
                                                            && session()->get("halaman")['8']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="product_id" value="<?= $usr->product_id; ?>" />
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
                                                            isset(session()->get("halaman")['8']['act_delete'])
                                                            && session()->get("halaman")['8']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="product_id" value="<?= $usr->product_id; ?>" />
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
                                                            isset(session()->get("halaman")['8']['act_create'])
                                                            && session()->get("halaman")['8']['act_create'] == "1"
                                                        )
                                                    ) { ?><?php if ($page == "paket") { ?>
                                                    <button
                                                        class="btn btn-sm btn-primary open-modal"
                                                        data-id="<?= $usr->product_id; ?>" data-name="<?= $usr->product_name; ?>">
                                                        <span class="fa fa-cubes" style="color:white;"></span>
                                                    </button>
                                                <?php } ?>
                                            <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $type[$usr->product_type]; ?></td>
                                            <td>
                                                <?= $usr->category_name; ?>
                                                <?php if ($usr->product_durasi > 0) { ?>
                                                    / <small class="text-danger"><?= $usr->product_standard; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td><?= $usr->product_urutan; ?></td>
                                            <td><?= $usr->unit_name; ?></td>
                                            <td>
                                                <?= $usr->product_name; ?> <?php if ($page == "paket") { ?>(<span class="fa fa-door" style="color:blue;"><?= $usr->product_roomqty; ?> room</span>)<?php } ?>
                                                <?php if ($usr->product_lanjutan > 0) { ?>
                                                    <br /><small class="text-danger">Lanjutan: <?= $usr->pname; ?></small>
                                                <?php } ?>
                                                <?php if ($usr->product_durasi > 0) { ?>
                                                    <br /><small class="text-danger">Durasi: <?= $usr->product_durasi; ?>m</small>
                                                    <br /><small class="text-danger">Alert: <?= $usr->product_dbend; ?>m</small>
                                                <?php } ?>
                                                <?php if ($page == "paket") { ?>
                                                    <br /><small class="text-primary" id="lbproduct_<?= $usr->product_id; ?>">
                                                        <?php $productd["productb.product_id"] = $usr->product_id;
                                                        $productb = $this->db->table("productb")
                                                            ->join("product", "product.product_id=productb.productb_bundle", "left")
                                                            ->orderBy("product.product_name ", "ASC")
                                                            ->getWhere($productd);
                                                        foreach ($productb->getResult() as $productb) { ?>
                                                            <div class="lproductb"><?= $productb->product_name ?> (<?= $productb->productb_qty ?>) <span onclick="confirmDelete(<?= $productb->productb_id ?>, <?= $usr->product_id ?>)" class="btn btn-xs btn-danger fa fa-close"></span></div>
                                                        <?php } ?>
                                                    </small>
                                                <?php } ?>
                                            </td>
                                            <?php
                                            $buy = 0;
                                            $sell = $usr->product_sell;
                                            $margin = $sell - $buy;
                                            ?>
                                            <?php if ($page == "produk") { ?>
                                                <td><?= $usr->product_ube; ?></td>
                                                <?php
                                                $limit = $usr->product_countlimit;
                                                $stock = $usr->product_stock;
                                                if ($limit >= $stock && $usr->product_type == 0) {
                                                    $alstock = "danger";
                                                    $salstock = "Peringatan";
                                                } else {
                                                    $alstock = "default";
                                                    $salstock = "Aman";
                                                }
                                                ?>
                                                <td><?= ($usr->product_type == 0) ? number_format($limit, 0, ".", ",") : ""; ?></td>
                                                <td><?= ($usr->product_type == 0) ? number_format($stock, 0, ".", ",") : ""; ?></td>
                                                <td class="text-<?= $alstock; ?>"><?= $salstock; ?></td>
                                                <td><?= ($usr->product_type == 0) ? number_format($buy, 0, ".", ",") : ""; ?></td>
                                            <?php } ?>
                                            <td><?= number_format($sell, 0, ".", ","); ?></td>
                                            <td><?= number_format($margin, 0, ".", ","); ?></td>
                                            <td>
                                                <?php if ($usr->product_durasi > 0) { ?>
                                                    <br /><small class="text-danger">Therapist: <?= $usr->product_profittherapist; ?></small>
                                                    <br /><small class="text-danger">Trainer: <?= $usr->product_profittrainer; ?></small>
                                                    <br /><small class="text-danger">Sales: <?= $usr->product_profitsales; ?></small>
                                                <?php } ?>
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
    function dlistbproduct(productb_id, product_id) {
        // alert('<?= base_url('dlistbproduct') ?>?productb_id=' + productb_id + '&product_id=' + product_id);
        $.get("<?= base_url('dlistbproduct') ?>", {
                productb_id: productb_id,
                product_id: product_id,
            })
            .done(function(data) {
                $("#lbproduct_" + product_id).html(data);
            });

    }

    function confirmDelete(productb_id, product_id) {
        if (confirm("Yakin mau hapus data ini?")) {
            dlistbproduct(productb_id, product_id);
        }
    }
    // klik tombol -> buka modal
    $('.open-modal').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#selected_product_id').val(id);
        $('#selected_product_name').html(name);
        $('#modalProduct').modal('show');
    });

    // submit modal
    $('#submitProduct').on('click', function() {
        var product_id = $('#selected_product_id').val();
        var bproduct_id = $('#product_select').val();
        var productb_qty = $('#productb_qty').val();
        var bproduct_text = $('#product_select option:selected').text();

        if (product_id == "") {
            alert("Pilih product dulu!");
            return;
        }

        $.get("<?= base_url('bproduct') ?>", {
                product_id: product_id,
                bproduct_id: bproduct_id,
                productb_qty: productb_qty,
            })
            .done(function(data) {
                $("#lbproduct_" + product_id).html(data);
                $('#modalProduct').modal('hide');
                $('#product_select').val("");
            });
    });

    $('.select').select2();
    var title = "Master <?= $title; ?>";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>