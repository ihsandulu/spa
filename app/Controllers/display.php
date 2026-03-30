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

    public function room()
    {
        $room = $this->db->table("product")
            ->join("category", "category.category_id=product.category_id", "left")
            ->join("(SELECT product_lanjutan AS pid, product_name AS pname FROM product)productlanjutan", "productlanjutan.pid=product.product_id", "left")
            ->where("product.category_id", "100")
            ->where("product_lanjutan", "0")
            ->orderBy("product_urutan", "ASC")
            ->get();
        $status = "secondary";
        $category_id = 0;
        $noc = 0;
        $warna = array("success", "warning", "info", "primary", "danger", "secondary", "dark", "light");
        $nstatus = "";
        foreach ($room->getResult() as $room) {
            $sisa_detik = 0;
            $sisa_waktu = ""; // default
            if ($room->product_status == 0) {
                if ($room->transaction_id > 0) {
                    $now = strtotime(date("Y-m-d H:i:s"));
                    $end = strtotime($room->product_bend);
                    $sisa_detik = $end - $now;
                    // pastikan tidak negatif
                    if ($sisa_detik < 0) $sisa_detik = 0;
                    // hitung jam:menit:detik
                    $jam = floor($sisa_detik / 3600);
                    $menit = floor(($sisa_detik % 3600) / 60);
                    $detik = $sisa_detik % 60;
                    $tampilsisa = 0;
                    $sisa_waktu = sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
                    if (date("Y-m-d H:i:s") >= $room->product_start && date("Y-m-d H:i:s") < $room->product_bend) {
                        $status = "success";
                        $nstatus = "TERISI";
                        $bgtime = "#ddf3e4;";
                        $tampilsisa = 1;
                    } elseif (date("Y-m-d H:i:s") >= $room->product_bend && date("Y-m-d H:i:s") <= $room->product_end) {
                        $status = "warning";
                        $nstatus = "AKAN HABIS";
                        $bgtime = "#f9f7e1;";
                        $tampilsisa = 1;
                    } elseif (date("Y-m-d H:i:s") > $room->product_end && date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime($room->product_end . " + 10 minute"))) {
                        $status = "danger";
                        $nstatus = "SELESAI";
                        $bgtime = "#f7d7d7;";
                        $tampilsisa = 1;
                    } else {
                        $status = "secondary";
                        $nstatus = "KOSONG";
                        $tampilsisa = 0;
                    }
                } else {
                    $status = "secondary";
                    $nstatus = "KOSONG";
                    $tampilsisa = 0;
                }
            } elseif ($room->product_status == 1) {
                $status = "light";
                $nstatus = "DIBERSIHKAN";
                $tampilsisa = 0;
            } elseif ($room->product_status == 2) {
                $status = "dark";
                $nstatus = "RUSAK";
                $tampilsisa = 0;
            } else {
                $status = "secondary";
                $nstatus = "KOSONG";
                $tampilsisa = 0;
            }
?>
            <?php if ($room->category_id != $category_id) { ?>
                <div class="col-12">
                    <!-- <h3 class="col-12 p-0"><span class="badge badge-<?= $warna[$noc++]; ?> col-12 category_name"><?= $room->category_name; ?></span></h3> -->
                    <h3 class="col-12 p-0"><span class="badge badge-<?= $warna[$noc++]; ?> col-12 "></span></h3>
                </div>
            <?php $category_id = $room->category_id;
            } ?>
            <div class="col-lg-2 p-2 rounded">
                <div class="room rounded">
                    <div class="carddeckbg bg-<?= $status; ?> inherit rounded">
                    </div>
                    <div class="carddeck rounded"><br />
                        <div class="text rounded p-2 text-center row">
                            <div class="judul col-12"><?= $room->product_name; ?></div>
                            <div class="subjudul1 text-<?= $status; ?> col-12"><?= $room->customer_name; ?></div>
                            <div class="col-12 p-1 d-grid">
                                <btn onclick="roomstatus(<?= $room->product_id; ?>,0);" class="btn btn-sm btn-<?= $status; ?> btn-block"><?= $nstatus; ?></btn>
                                <?php if ($tampilsisa == 1) { ?>
                                    <div style="margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; background-color: <?= $bgtime; ?>; padding: 5px; font-size: 14px; color: #333;">
                                        <?= $sisa_waktu; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php }
    }

    public function roomstatus()
    {
        $input["product_start"] = "0000-00-00 00:00:00";
        $input["product_bend"] = "0000-00-00 00:00:00";
        $input["product_end"] = "0000-00-00 00:00:00";
        $input["product_status"] = $this->request->getGet("product_status");
        $where["product_id"] = $this->request->getGet("product_id");
        $this->db->table("product")
            ->update($input, $where);
    }
}
