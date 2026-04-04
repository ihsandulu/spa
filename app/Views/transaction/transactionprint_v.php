<?php
$this->session = \Config\Services::session();
$this->request = \Config\Services::request();

use Config\Database;

$this->db = Database::connect("default");
if ($this->session->get('user_id') == "") {
    $this->session->setFlashdata("message", "Selamat Datang !");
    header('Location:' . base_url('login?message=Silahkan Login !'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/icons/logo.png">
    <title>POS</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.4.5.2.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
    <!--[if lt IE 9]>
    <script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->





    <!--Custom JavaScript -->
    <script src="js/custom.min.js"></script>

    <style>
        .border {
            border: black solid 1px !important;
        }

        .bold {
            font-weight: bold;
        }


        .separator {
            border-bottom: 1px dashed #aaa;
        }

        .text-small {
            font-size: 8px;
        }

        .img_product {
            width: 100%;
            height: 150px !important;
            border: rgba(155, 155, 155, 0.5) solid 1px;
            border-radius: 4px;
        }

        .pointer {
            cursor: pointer;
        }

        .centerpage {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .hide {
            display: none;
        }

        .absolute-top-right {
            position: absolute;
            right: 5px;
            top: 5px;
        }

        @media print {

            html,
            body {
                margin: 0px !important;
                padding: 0px !important;
                position: relative;
                top: 0px;
            }

            html,
            body,
            div {
                font-size: 60px;
                margin: 0px !important;
                line-height: 70px !important;
                color: black !important;
            }

            .poppins {
                font-family: "Poppins", sans-serif;
            }

            .franklin {
                font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            }

            th,
            td {
                color: black !important;
                padding: 0px 1px 0px 1px;
                font-size: 60px !important;
                line-height: 100% !important;
                border: black solid 1px !important;
            }


            #storename_title {
                margin: bottom 30px, im !important;
            }

            p {
                color: black !important;
                font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
                padding: 0px 1px 0px 1px;
                font-size: 60px !important;
                line-height: 100% !important;
            }

            @page {}

            .tebal10 {
                font-size: 50px;
                font-weight: bold;
            }

            .tebal12 {
                font-size: 52px;
                font-weight: bold;
            }

            .tebal14 {
                font-size: 54px;
                font-weight: bold;
            }

            .tebal16 {
                font-size: 56px;
                font-weight: bold;
            }

            .mb5 {
                margin-bottom: 5px !important;
            }

            .mb10 {
                margin-bottom: 10px !important;
            }


            .mb20 {
                margin-bottom: 20px !important;
            }

            .mb25 {
                margin-bottom: 25px !important;
            }

            .mb30 {
                margin-bottom: 30px !important;
            }

            .mb35 {
                margin-bottom: 35px !important;
            }

            .pagebreak {
                page-break-after: always;
            }

            .catatan {
                font-size: 60px !important;
            }

            /* Paksa warna supaya tidak hilang */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .borderbtm {
                border-bottom: rgba(46, 44, 44, 0.73) solid 1px !important;
            }

            .bordertop {
                border-top: rgba(46, 44, 44, 0.73) solid 1px !important;
            }

            .border-kiri {
                border-left: rgba(46, 44, 44, 0.73) solid 1px !important;
            }

            .border-kanan {
                border-right: rgba(46, 44, 44, 0.73) solid 1px !important;
            }
        }

        .border {
            border: black solid 1px;
        }

        .line {
            width: 100%;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            height: 10px;;
        }
        .isi{font-size: 50px!important;}
    </style>


</head>

<body class="fix-header fix-sidebar">

    <?php
    $store = $this->db->table("store")->where("store_id", session()->get("store_id"))->get()->getRow();
    $builder = $this->db->table("transaction")
        ->where("transaction_id", $this->request->getGet("transaction_id"));
    $transaction = $builder->get();
    if ($builder->countAll() > 0) {
        foreach ($transaction->getResult() as $transaction) {
    ?>
            <div class='container-fluid'>
                <div class='row'>
                    <div class="col-12 row" style=" border-top:black solid  1px;  padding-top:15px; padding-bottom:15px;">
                        <div class="col-12 text-center mb30 franklin" id="storename_title"><?= $store->store_name; ?></div>
                        <div class="col-12 text-center mb5 poppins"><?= $store->store_address; ?></div>
                        <div class="col-12 text-center mb5 poppins">Mobile : <?= $store->store_phone; ?>, <?= session()->get("store_web"); ?></div>
                    </div>
                    <div class="line"></div>
                    <div class="mb-1 mt-1 p-1 tebal10 text-center col-12">
                        <?= $transaction->transaction_no; ?> - <?= date("d M Y", strtotime($transaction->transaction_date)); ?>
                    </div>
                    <div class="col-12 bordertop" style="padding:0px;">

                        <div class="row pb-1 mb-1 borderbtm text-center bold">
                            <div class="col-4">Produk</div>
                            <div class="col-4 border-kiri border-kanan">Harga</div>
                            <div class="col-4">Total</div>
                        </div>
                        <?php
                        $usr = $this->db
                            ->table("transactiond")
                            ->select("*,SUM(transactiond_qty)AS qty, SUM(transactiond_price)AS price,")
                            ->join("product", "product.product_id=transactiond.product_id", "left")
                            ->join("unit", "unit.unit_id=product.unit_id", "left")
                            ->where("product.store_id", session()->get("store_id"))
                            ->where("transactiond.transaction_id", $this->request->getGet("transaction_id"))
                            ->groupBy("transactiond.product_id")
                            ->orderBy("product_name", "ASC")
                            ->get();
                        //echo $this->db->getLastquery();
                        $no = 1;
                        $tprice = 0;
                        foreach ($usr->getResult() as $usr) {
                            if ($usr->transactiond_foc > 0) {
                                $diskon = $usr->transactiond_price;
                                $kdis = "FOC";
                            } else 
                            if ($usr->transactiond_nominal > 0) {
                                $diskon = $usr->transactiond_nominal;
                                $kdis = "Nominal";
                            } else 
                            if ($usr->transactiond_percent > 0) {
                                $diskon = $usr->transactiond_percent / 100 * $usr->transactiond_price;
                                $kdis = $usr->transactiond_percent . "%";
                            } else {
                                $diskon = 0;
                                $kdis = "";
                            }
                            $qty = $usr->qty;
                            $price = $usr->price;
                            $stprice = $price - $diskon;
                            $tprice += $stprice;

                            if ($usr->product_start == null) {
                                $product_start = "0000-00-00 00:00:00";
                            }
                            if ($usr->user_id == null) {
                                $user_id = 0;
                            }
                        ?>
                            <div class="row borderbtm pb-1 mb-1">
                                <div class="col-4 text-center isi">
                                    <?= $usr->product_name; ?><!-- <br />
                                        <?= $usr->product_batch; ?> -->
                                </div>
                                <div class="col-4 text-center border-kiri border-kanan isi">
                                    <?= number_format($price, 0, ",", ".") ?> (<?= number_format($qty, 0, ",", ".") ?>)<br />
                                    <?php if ($diskon > 0) { ?><small>Disc.<?= number_format($diskon, 0, ",", "."); ?></small><?php } ?>
                                </div>
                                <div class="col-4 text-center isi">
                                    <?= number_format($stprice, 0, ",", ".") ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row borderbtm pb-1 mb-1">
                            <div class="col-8 text-right border-kanan pr-3 isi">
                                <strong>Total</strong>
                            </div>
                            <div class="col-4 text-center isi">
                                <?= number_format($tprice, 0, ",", "."); ?>
                                <input type="hidden" id="tagihan" value="<?= $tprice; ?>" />
                            </div>
                        </div>
                        <div class="row borderbtm pb-1 mb-1">
                            <div class="col-8 text-right border-kanan pr-3 isi">
                                <strong>Bayar</strong>
                            </div>
                            <div class="col-4 text-center isi">
                                <?= number_format($transaction->transaction_pay, 0, ",", "."); ?>
                            </div>
                        </div>
                        <div class="row borderbtm pb-1 mb-1">
                            <div class="col-8 text-right border-kanan pr-3 isi">
                                <strong>Kembalian</strong>
                            </div>
                            <div class="col-4 text-center">
                                <?= number_format($transaction->transaction_change, 0, ",", "."); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3 pt-0 text-center catatan">
                        <?= $store->store_noteinvoice; ?>
                    </div>
                    <!--  <div class="col-4 row mt-5 p-0" style=""  align="center">
                <div class="col-12"><strong class="tebal10">Hormat Kami,</strong></div>
                <div class="col-12" style="height:50px;">&nbsp;</div>
                <div class="col-12" style=""><strong><?= session()->get("user_name"); ?></strong></div>
            </div> -->
                </div>
            </div>

        <?php }
    } else { ?>
        <h1 class="centerpage">Data tidak ditemukan!</h1>
    <?php } ?>
    <script>
        window.print();
        setTimeout(function() {
            this.close();
        }, 500);
    </script>



</body>

</html>