<?php echo $this->include("template/header_v"); ?>
<style>
.caption-1 figcaption {
  position: absolute;
  bottom: 0;
  right: 0;
}
.caption-2 figcaption {
  width: 80%;
  position: absolute;
  bottom: 1rem;
  left: 10%;
  background: rgba(255, 255, 255, 0.6);
}
.caption-3 figcaption {
  position: absolute;
  bottom: 0;
  right: 0;
  transform: translateY(-50%);
}
.separator {
  border-bottom: 1px dashed #aaa;
}
.text-small{font-size: 8px;}
.text-small0{font-size: 12px;}
.text-small1 {
  font-size: 14px;
}
.text-small2 {
  font-size: 15px;
}
.img_product{
    width:100%; 
    height:150px !important;    
  border:rgba(155, 155, 155, 0.5) solid 1px;
  border-radius:4px;
}
.divimg_product{margin-bottom:10px; }
.pointer{cursor: pointer;}
.figcaption{background-color: rgba(0, 0, 0, 0.8); border-radius:2px; padding:5px; }
#listproduct{overflow-y: scroll;}
#listproduct::-webkit-scrollbar {
  display: none;
}
#listproduct {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
.centerpage{
    position: fixed;
    left:50%;
    top:50%;
    transform:translate(-50%,-50%);
}
.hide{display: none;}
#bayara{
    background-color:white; 
    padding:50px!important; 
    border:rgba(200,100,200,0.1) solid 1px; 
    border-radius:5px; 
    box-shadow:rgba(200,100,200,0.1) 0px 0px 5px 5px;
    z-index: 100;
}
.absolute-top-right{
    position: absolute;
    right:5px;
    top:5px;
}
.disabled{opacity: 0.1;}
.mb-10{margin-bottom:-10px;}
.mb-20{margin-bottom:-20px;}
.dot{
  height: 15px!important;
  width: 15px!important;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  position:relative;
  left:0px;
  top:0px;
}
</style>
<script>
function cekproductlanjutan(transaction_id){ 
    $("#tempscript").html("");
    // alert("<?=base_url();?>/cekproductlanjutan?transaction_id="+transaction_id);    
    $.get("<?=base_url();?>/cekproductlanjutan",{transaction_id:transaction_id})
    .done(function(data){
        setTimeout(() => {
            $("#tempscript").html(data);  
        }, 500);        
    });
}
</script>
<div id="tempscript"></div>

<div class='container-fluid'>
    <div class='row'>
        <div class='col-7'>
            <div class="card">
                <div class="card-body">
                    <div class="row"> 
                        <div class="col-6 row">                       
                            <div class="col-6">
                                <button onclick="plistproduct('gambar','');" class="btn btn-info btn-block">Bergambar</button>
                            </div>                               
                            <div class="col-6">
                                <button onclick="plistproduct('list','');" class="btn btn-info btn-block">List Data</button>
                            </div>                        
                        </div>                       
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <input type="hidden" id="typesearch" value="gambar"/>
                                <input  onfocusin="$('#fokus').val('cari')"  onkeyup="cariproduk();" id="cariproduk" type="text" class="form-control" placeholder="Cari Produk" aria-label="Cari Produk" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary fa fa-search" type="button"></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 row" id="listproduct">
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class='col-5'>
            <div class="card">
                <div class="card-body row">
                    <input id="fokus" type="hidden" value="barcode"/>
                    <div id="test"></div>
                    <div class="col-12">                       
                        <div class="input-group mb-3">
                            <input onfocusin="$('#fokus').val('barcode')" id="inputbarcode" autofocus type="text" class="form-control" placeholder="Scan Barcode" aria-label="Scan Barcode" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary fa fa-edit" type="button"></button>
                            </div>
                        </div>                       
                    </div>
                    <div class="col-12 row">                          
                        <input type="hidden" id="modalstatus" value="keluar"/>  
                        <input type="hidden" id="listnotastatus" value="2"/>                          
                        <input type="hidden" id="kasterakhirval"/>                        
                        <input type="hidden" id="kasshift" value="0"/>
                        <div class="col-12 p-0">                             
                            <button id="btnmodalawal"  data-toggle="tooltip" data-placement="top" title="Modal Awal dari Owner" onclick="modalkas('masuk');" class="btn  btn-success fa fa-money mb-2" type="button"></button>   
                            <button  data-toggle="tooltip" data-placement="top" title="Stor Uang ke Owner" onclick="modalkas('keluar');" class="btn fa fa-mail-forward btn-danger mb-2 btn-child" type="button">
                                <!-- <span class="fa-stack fa-xs">
                                    <i class="fa fa-money fa-stack-1x"></i>
                                    <i class="fa fa-ban fa-stack-2x text-danger"></i>
                                </span> -->
                            </button>    
                            <button  data-toggle="tooltip" data-placement="top" title="Transaksi Pending" onclick="listnota(2);nota(0);" class="btn  btn-warning fa fa-flag-checkered mb-2 btn-child" type="button"></button>
                            <button  data-toggle="tooltip" data-placement="top" title="Transaksi Sukses" onclick="listnota(0);nota(0);" class="btn  btn-success fa fa-check mb-2 btn-child" type="button"></button>
                            <button  data-toggle="tooltip" data-placement="top" title="Refresh Halaman" onclick="refresh();" class="btn  btn-info fa fa-refresh mb-2 btn-child" type="button"></button>
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
                                <button data-toggle="tooltip" data-placement="top" title="Buat Nota Baru" onclick="createnota();" class="btn  btn-primary fa fa-plus mb-2 btn-child" type="button"></button>
                                <?php if(session()->get("store_member")==1){?>
                                    <button data-toggle="tooltip" data-placement="top" title="Masukkan Member" onclick="member();" class="btn  btn-primary fa fa-user mb-2 btn-child" type="button"></button>
                                <?php }?>
                            <?php }?>
                        </div>
                        <div id="keterangan" class="alert alert-info col-12  p-1 text-center" role="alert"></div>           
                        
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
                            <input onchange="listnota(-1)" type="date" id="from" name="from" class="form-control" value="<?=$from;?>">&nbsp;
                            <label for="to">Ke:</label>&nbsp;
                            <input onchange="listnota(-1)" type="date" id="to" name="to" class="form-control" value="<?=$to;?>">&nbsp;
                            <button onclick="hariini()" type="button" class="btn btn-primary">Hari Ini</button>
                        </form>
                        
                        <div class="my-1" id="listnota"></div>  
                        <div class="separator my-3"></div>                    
                    </div>
                    <div class="col-12" id="nota">
                        
                    </div>                    

                    <div  onclick="fokus('bayar')" onfocusout="fokus('barcode');" class="modal " id="bayar">                       
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Pembayaran</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-7">
                                            Tagihan : Rp. <span class="bill"></span>
                                        </div>
                                        <div class="col-5">
                                            <button id="btnpending" onclick="pendingbill();" type="button" class="btn btn-sm btn-warning">Pending Bill</button>  
                                            <button id="btnbayar" onclick="bayarbill();" type="button" class="btn btn-sm btn-success">Bayar Bill</button>   
                                        </div>
                                    </div>
                                    <div class="pt-3" id="pbill">
                                        <div class="form-group">
                                            <label for="transaction_pending">Penanggung:</label>
                                            <select id="transaction_pending" class="form-control" >
                                                <?php $user=$this->db->table("user")
                                                ->where("store_id",session()->get("store_id"))
                                                ->where("user_penanggung","1")
                                                ->orderBy("user_name")->get();
                                                foreach ($user->getResult() as $user) {
                                                    if($user->user_id==2){$selected="selected";}else{$selected="";}?>                                                    
                                                <option value="<?=$user->user_id;?>" <?=$selected;?>><?=$user->user_name;?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="pt-3" id="bbill">
                                        <div class="form-group">
                                            <label for="account_id">Tipe Pembayaran:</label>
                                            <select onchange="cekpembayaran();" id="account_id" class="form-control" >
                                                <?php $account=$this->db->table("account")
                                                ->where("store_id",session()->get("store_id"))
                                                ->where("account_ispembayaran","1")
                                                ->orderBy("account_id")->get();
                                                foreach ($account->getResult() as $account) {
                                                    if($account->account_id==2){$selected="selected";}else{$selected="";}?>                                                    
                                                <option value="<?=$account->account_id;?>" <?=$selected;?>><?=$account->account_name;?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        <div class="form-group cash">
                                            <label for="uang" class="bg-warning p-2">Cash:</label> &nbsp
                                            <input onkeyup="rupiahnumerik(this);" change="kembalian('cash');" onclick="fokus('bayar')" type="number" class="form-control" ids="uang" id="cash" name="cash">
                                            <script>rupiahnumerik($("#cash"))</script>
                                        </div>
                                        <div class="form-group nucard1">
                                            <label for="nucard1" class="bg-success p-2">Card Number (1):</label> &nbsp
                                            <input type="text" class="form-control" id="nucard1">
                                        </div>
                                        <div class="form-group nocard1">
                                            <label for="nocard1">Nominal Card (1):</label> &nbsp
                                            <input onkeyup="rupiahnumerik(this);" change="kembalian('nocard1');" onclick="fokus('bayar')" type="number" class="form-control" id="nocard1" name="nocard1">
                                            <script>rupiahnumerik($("#nocard1"))</script>
                                        </div>
                                        <div class="form-group nucard2">
                                            <label for="nucard2" class="bg-info p-2">Card Number (2):</label> &nbsp
                                            <input type="text" class="form-control" id="nucard2">
                                        </div>
                                        <div class="form-group nocard2">
                                            <label for="nocard2">Nominal Card (2):</label> &nbsp
                                            <input onkeyup="rupiahnumerik(this);" change="kembalian('nocard2');" onclick="fokus('bayar')" type="number" class="form-control" id="nocard2" name="nocard2">
                                            <script>rupiahnumerik($("#nocard2"))</script>
                                        </div>
                                    </div>
                                    <div class="pt-3">
                                        <input onchange="kembalian1('uang');" type="hidden" class="form-control" id="uang">
                                        <button onclick="pelunasan();" type="button" class="btn btn-primary">Submit</button>   
                                    </div>
                                    <div>Pembayaran : Rp. <span class="dibayar"></span></div>
                                    <div>Kembalian : Rp. <span class="kembalian"></span></div>
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function cekpembayaran(){
                            let pembayaran=$("#account_id").val();
                            $("#uang").val(0);
                            $("#cash").val("0");
                            $("#nocard1").val("0");
                            $("#nocard2").val("0");
                            $("#cash1").val("0");
                            $("#nocard11").val("0");
                            $("#nocard21").val("0");
                            $(".nucard1").hide().val("");
                            $(".nucard2").hide().val("");

                            //cash
                            if(pembayaran==101){
                                $(".cash").show();
                                $(".nucard1").hide().val("");
                                $(".nocard1").hide().val("0");
                                $(".nucard2").hide().val("");
                                $(".nocard2").hide().val("0");
                            }
                            //card
                            if(pembayaran==102){
                                $(".cash").hide().val("0");
                                $(".nucard1").show();
                                $(".nocard1").show();
                                $(".nucard2").hide().val("");
                                $(".nocard2").hide().val("0");
                            }
                            //cash&card
                            if(pembayaran==103){
                                $(".cash").show();
                                $(".nucard1").show();
                                $(".nocard1").show();
                                $(".nucard2").hide().val("");
                                $(".nocard2").hide().val("0");
                            }
                            //card&card
                            if(pembayaran==104){
                                $(".cash").hide().val("0");
                                $(".nucard1").show();
                                $(".nocard1").show();
                                $(".nucard2").show();
                                $(".nocard2").show();
                            }
                        }
                        cekpembayaran();                        
                        
                        function kembalian(bayar1){
                            let pembayaran=$("#account_id").val();
                            let uang = 0 ;
                            let cash1 = parseInt($("#cash1").val());
                            let nocard11 = parseInt($("#nocard11").val());
                            let nocard21 = parseInt($("#nocard21").val());
                            //cash
                            if(pembayaran==101){
                               uang = cash1;
                            }
                            //card
                            if(pembayaran==102){
                               uang = nocard11;                                
                            }
                            //cash&card
                            if(pembayaran==103){
                               uang = cash1+nocard11;                                 
                            }
                            //card&card
                            if(pembayaran==104){
                               uang = nocard11+nocard21;   
                            }
                            $("#uang").val(uang);
                            setTimeout(() => {
                                kembalian1('uang');
                            }, 200);

                        }

                        function kembalian1(bayar1){
                            let bayar = $("#"+bayar1+"").val();
                            let tagihan = $("#tagihan").val();
                            let kembalian = bayar-tagihan;
                            $("#kembaliannya").val(kembalian);
                            $("#bayarannya").val(bayar);
                            // alert(kembalian);
                            $(".dibayar").html(formatRupiah(bayar));
                            $(".kembalian").html(formatRupiah(kembalian));
                        }

                        function pendingbill(){           
                            $("#transaction_pending").val(0);
                            $("#bbill").hide();
                            $("#pbill").show();
                            $("#penanggung").show();
                            $("#btnpending").hide();
                            $("#btnbayar").show();
                        }

                        function bayarbill(){                            
                            $("#transaction_pending").val(0);
                            $("#bbill").show();
                            $("#pbill").hide();
                            $("#penanggung").hide();
                            $("#btnpending").show();
                            $("#btnbayar").hide();
                        }
                        bayarbill();
                    </script>
                    <div onclick="fokus('modalawal');" onfocusout="fokus('barcode');" class="modal " id="showmodalawal">                       
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Kas Masuk</h4>
                                </div>
                                <div class="modal-body">
                                    <div>Shift : <span class="kasshift"></span></div>
                                    <div>Modal Awal : Rp. <span class="kasawal"></span></div>
                                    <div>Kas Terakhir : Rp. <span class="kasterakhir"></span></div>
                                    <div class="form-inline">
                                        <label for="modalawal">Jumlah Uang:</label> &nbsp
                                        <input type="number" class="form-control" id="modalawal"> &nbsp
                                        <button onclick="kasmodal('masuk')" type="button" class="btn btn-primary">Submit</button>
                                    </div>
                                    <div>Keterangan : <span class="keteranganmodalawal">Modal awal dari owner.</span></div>
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick="fokus('modalakhir');" onfocusout="fokus('barcode');" class="modal " id="showmodalakhir">                       
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Kas Keluar</h4>
                                </div>
                                <div class="modal-body">
                                    <div>Shift : <span class="kasshift"></span></div>
                                    <div>Modal Awal : Rp. <span class="kasawal"></span></div>
                                    <div>Kas Terakhir : Rp. <span class="kasterakhir"></span></div>
                                    <div class="form-inline">
                                        <label for="modalakhir">Jumlah Uang:</label> &nbsp
                                        <input type="number" class="form-control" id="modalakhir"> &nbsp
                                        <button onclick="kasmodal('keluar')" type="button" class="btn btn-primary">Submit</button>
                                    </div>
                                    <div>Keterangan : <span class="keteranganmodalakhir">Stor uang kepada owner.</span></div>
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick=""  class="modal " id="showmember">                       
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Member</h4>
                                </div>
                                <div class="modal-body" id="listmember">
                                    <form class="form-inline" action="/action_page.php">
                                        <label for="member_no" class="mr-sm-2">Member No.:</label>
                                        <input onkeyup="carimember('member_no',this.value)" type="text" class="form-control mb-2 mr-sm-2" placeholder="Masukkan Whatsapp" id="member_no">
                                        <label for="member_name" class="mr-sm-2">Name:</label>
                                        <input onkeyup="carimember('member_name',this.value)" type="text" class="form-control mb-2 mr-sm-2" placeholder="Masukkan Nama" id="member_name">
                                    </form>    
                                    <div class="table-responsive m-t-40" id="listmembernya">
                            
                                    </div>                                
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div onclick="fokus('insertqty');"  class="modal" id="jmlnota">  -->
                    <div onclick=""  class="modal" id="jmlnota">                       
                        <div class="modal-dialog modal-xs">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Tambah Jumlah Produk</h4>
                                </div>
                                <div class="modal-body" id="listmember">
                                    <div>
                                        <div class="form-group mb-0">
                                            <label for="member_name" class="">Jml:</label>
                                            <input onkeyup="rupiahnumerik(this);" type="number" class="form-control" placeholder="Masukkan Jumlah" id="qtyproduct" name="qtyproduct" value="1"> &nbsp
                                            <!-- ID Produk -->
                                            <input id="qtyproduct_id" value="0" type="hidden"/>
                                            <script>rupiahnumerik($("#qtyproduct"))</script>
                                        </div>
                                        <div class="form-group mb-0 hide" id="pstart">
                                            <label for="member_name" class="">Start:</label>
                                            <input type="datetime-local" class="form-control" id="start"> &nbsp
                                        </div>

                                        <div class="form-group hide" id="ptherapist">
                                            <label for="xtherapist" class="">Therapist:</label>
                                            <select class="form-control" id="xtherapist">
                                                <option value="">Pilih Therapist</option>
                                                <?php $therapist=$this->db->table("category")
                                                ->join("position","position.position_id=category.position_id","left")
                                                ->join("user","user.position_id=position.position_id","left")
                                                ->where("category.position_id >","0")
                                                ->groupBy("user.user_id")
                                                ->get();
                                                foreach($therapist->getResult() as $xtherapist){?>
                                                    <option value="<?=$xtherapist->user_id;?>"><?=$xtherapist->user_name;?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        <h2>Diskon</h2>
                                        <div class="form-group mb-3">
                                            <label for="xfoc" class="text-danger">FOC:</label>
                                            <select onchange="focon()" class="form-control" id="xfoc">
                                                <option value="0">Tidak</option>
                                                <option value="1">Iya</option>
                                            </select>
                                            <script>
                                                function focon(){
                                                    let xfoci = $('#xfoc').val();
                                                    if(xfoci==0){
                                                        $('.nfoc').show();
                                                    }else{
                                                        $('.nfoc').hide();
                                                    }
                                                }
                                                focon();
                                            </script>
                                        </div>
                                        <div class="form-group mb-0 nfoc" id="pnominal">
                                            <label for="xnominal" class="text-primary">Nominal:</label>
                                            <input onkeyup="rupiahnumerik(this);" change="ceknomper('inom');" type="number" class="form-control" id="xnominal" name="xnominal"> &nbsp
                                            <script>rupiahnumerik($("#xnominal"))</script>
                                        </div>
                                        <div class="form-group mb-0 nfoc" id="ppercent">
                                            <label for="xpercent" class="text-primary">Persentase:</label>
                                            <input onkeyup="rupiahnumerik(this);" change="ceknomper('iper');" type="number" class="form-control" id="xpercent" name="xpercent"> &nbsp
                                            <script>rupiahnumerik($("#xpercent"))</script>
                                        </div>
                                        <script>
                                            function ceknomper(tipe){
                                                let inom = $('#xnominal').val();
                                                let iper = $('#xpercent').val();
                                                if(inom>0&&tipe=='inom'){
                                                    $('#xpercent').val(0);
                                                    $('#xfoc').val(0);
                                                    $('#xpercent1').val(0);
                                                    $('#xfoc1').val(0);
                                                }
                                                if(iper>0&&tipe=='iper'){
                                                    $('#xnominal').val(0);
                                                    $('#xfoc').val(0);
                                                    $('#xnominal1').val(0);
                                                    $('#xfoc1').val(0);
                                                }
                                            }
                                            focon();
                                        </script>
                                        
                                        <input id="wajib" value="" type="hidden"/>
                                        <input id="transactiond_id" value="0" type="hidden"/>
                                        <button onclick="insertnotaproduk();" type="button" class="btn btn-primary">Submit</button>
                                    </div>                               
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function hariini(){
                            $("#from").val('<?=date("Y-m-d");?>');
                            $("#to").val('<?=date("Y-m-d");?>');
                        }
                        function insertmember(transaction_id,member_id,customer_name){ 
                             $.get("<?=base_url("insertmember");?>",{transaction_id:transaction_id,member_id:member_id,customer_name:customer_name})
                            .done(function(data){ 
                                $("#showmember").modal('hide');
                                nota(transaction_id);
                            });   
                        }
                        function carimember(tipe,isi){
                            let transaction_id= $("#transaction_id").val();
                            let arraycarimember;
                            if(tipe=='member_no'){
                                arraycarimember={transaction_id:transaction_id,member_no:isi};
                            }else if(tipe=='member_name'){
                                arraycarimember={transaction_id:transaction_id,member_name:isi};
                            }
                            $.get("<?=base_url("listmember");?>",arraycarimember)
                            .done(function(data){ 
                                $("#listmembernya").html(data);
                            });     
                        }
                        function modalstatus(){
                            $.get("<?=base_url("posisishift");?>")
                            .done(function(data){ 
                                $("#modalstatus").val(data);
                                if(data=='keluar'){                             
                                    $(".btn-child").prop('disabled', true);
                                    $("#btnmodalawal").attr('data-original-title', 'Modal Awal dari Owner');
                                }
                                if(data=='masuk'){                                
                                    $(".btn-child").prop('disabled', false);     
                                    $("#btnmodalawal").attr('data-original-title', 'Update Modal Awal');
                                }
                            });                     
                        }
                        function modalnominal(){
                            $.get("<?=base_url("nominalkas");?>")
                            .done(function(data){                              
                                 $("#kasterakhirval").val(data);   
                                 $(".kasterakhir").html(formatRupiah(data));
                            });                   
                        }
                        function modalawal(){
                            $.get("<?=base_url("modalawalkas");?>")
                            .done(function(data){                  
                                 $(".kasawal").html(formatRupiah(data));
                            });                   
                        }
                        function shift(){
                            $.get("<?=base_url("shift");?>")
                            .done(function(data){                  
                                 $(".kasshift").html(data);          
                                 $("#kasshift").val(data);
                            });                   
                        }
                        function datamodalkas(){
                            //realtime kas
                            modalnominal();
                            //modal awal
                            modalawal();
                            //shift
                            shift();
                        }
                        </script>
                        <script>
                        function modalkas(type){
                            if(type=='masuk'){
                                $("#showmodalawal").modal();
                                fokus('modalawal');
                            }
                            if(type=='keluar'){
                                $("#showmodalakhir").modal();
                                fokus('modalakhir');
                                $('#modalakhir').val(0).focus();
                            }

                            datamodalkas();
                            
                        }
                        function member(){
                            $("#showmember").modal();
                            fokus('memberno');  
                                                     
                        }
                        </script>
                        <script>
                        function kasmodal(kas_type){
                            let store_id=<?=session()->get("store_id");?>;
                            let ok = confirm('Are you sure?');
                            let en = false;
                            let kasterakhir = $("#kasterakhirval").val(); 
                            // alert(ok);
                            let kas_nominal=0;
                            if(ok==true){
                                if(kas_type=='masuk'){
                                // alert(kas_type);
                                   kas_nominal=$("#modalawal").val();
                                    $("#showmodalawal").modal('hide');
                                    $('#modalawal').val(0);
                                    en = true;
                                }else if(kas_type=='keluar'){
                                    kas_nominal=$("#modalakhir").val();
                                    kas_nominal=parseInt(kas_nominal);
                                    if(kasterakhir==kas_nominal){
                                        $("#showmodalakhir").modal('hide');
                                        $('#modalakhir').val(0);
                                        en = true;
                                    }else{
                                        en = false;
                                        alert("Jumlah penarikan tidak sama dengan aktual kas!");
                                        setTimeout(function(){
                                           fokus('modalakhir');
                                           $("#modalakhir").val(0);
                                        },500);
                                    }
                                }
                                // alert(kas_type);
                                if(en==true){
                                    // alert("<?=base_url("kasmodal");?>?kas_type="+kas_type+"&kas_nominal="+kas_nominal+"&store_id="+store_id);
                                    $.get("<?=base_url("kasmodal");?>",{kas_type:kas_type,kas_nominal:kas_nominal,store_id:store_id})
                                    .done(function(data){
                                        alert(data);
                                        modalstatus();
                                    });
                                    setTimeout(function(){
                                        datamodalkas();
                                    },1000);
                                }
                            }
                        }

                        </script>
                        <script>
                        function fokus(type){
                            switch (type) {
                                case 'barcode':
                                    $("#inputbarcode").focus();
                                    $("#fokus").val("barcode"); 
                                break;         
                                case 'cari':
                                    $("#cariproduk").focus();
                                    $("#fokus").val("cari"); 
                                break;         
                                case 'bayar':
                                    // $("#uang").focus();
                                    $("#fokus").val("bayar"); 
                                break;          
                                case 'modalawal':
                                    $("#modalawal").focus();
                                    $("#fokus").val("modalawal"); 
                                break;            
                                case 'modalakhir':
                                    $("#modalakhir").focus();
                                    $("#fokus").val("modalakhir"); 
                                break;              
                                case 'memberno':
                                    $("#member_name").val('');
                                    $('#member_no').focus(); 
                                    $("#fokus").val("memberno"); 
                                break;               
                                case 'membername':
                                    $("#member_no").val('');
                                    $("#member_name").focus();
                                    $("#fokus").val("membername"); 
                                break;           
                                case 'insertqty':
                                    $("#fokus").val("insertqty"); 
                                    $("#qtyproduct").focus();
                                break;                                                               
                                default:
                                    $("#inputbarcode").focus();
                                    $("#fokus").val("barcode"); 
                                break;
                            }
                        }
                        </script>
                        <script>
                        function print(transaction_id){
                            window.open('<?=base_url("transactionprint?transaction_id=");?>'+transaction_id,'_blank');
                        }
                        function cekstatus(transaction_id){
                             $.get("<?=base_url("cekstatus");?>",{transaction_id:transaction_id})
                            .done(function(data){  
                                if(data==0){
                                    $("#printicon").show();
                                }else{
                                    $("#printicon").hide();
                                }
                            });
                        }
                        function pelunasan(){                           
                            let account_id = $("#account_id").val();
                            let transaction_id = $("#transaction_id").val();
                            let transaction_no = $("#transaction_no").val();
                            let transaction_bill = $("#tagihan").val();
                            let transaction_pay=$("#bayarannya").val();
                            let transaction_change=$("#kembaliannya").val();
                            let shift=$("#kasshift").val();
                            //metode pembayaran
                            let transaction_nominalcash=$("#cash1").val();
                            let transaction_numbercard1=$("#nucard1").val();
                            let transaction_nominalcard1=$("#nocard11").val();
                            let transaction_numbercard2=$("#nucard2").val();
                            let transaction_nominalcard2=$("#nocard21").val();

                            let transaction_pending=$("#transaction_pending").val();
                            

                            /* $("#test").html("<?=base_url("pelunasan");?>?account_id="+account_id
                            +"&transaction_id="+transaction_id
                            +"&transaction_bill="+transaction_bill
                            +"&transaction_pay="+transaction_pay
                            +"&transaction_change="+transaction_change
                            +"&shift="+shift
                            +"&transaction_no="+transaction_no
                            +"&transaction_nominalcash="+transaction_nominalcash
                            +"&transaction_numbercard1="+transaction_numbercard1
                            +"&transaction_nominalcard1="+transaction_nominalcard1
                            +"&transaction_numbercard2="+transaction_numbercard2
                            +"&transaction_nominalcard2="+transaction_nominalcard2
                            +"&transaction_pending="+transaction_pending); */
                            // alert();
                            
                            $.get("<?=base_url("pelunasan");?>",{
                                account_id:account_id,
                                transaction_id:transaction_id,
                                transaction_bill:transaction_bill,
                                transaction_pay:transaction_pay,
                                transaction_change:transaction_change,
                                shift:shift,
                                transaction_no:transaction_no,
                                transaction_nominalcash:transaction_nominalcash,
                                transaction_numbercard1:transaction_numbercard1,
                                transaction_nominalcard1:transaction_nominalcard1,
                                transaction_numbercard2:transaction_numbercard2,
                                transaction_nominalcard2:transaction_nominalcard2,
                                transaction_pending:transaction_pending
                            })
                            .done(function(data){       
                                // alert(data); 
                                setTimeout(() => {
                                    updatestatus(transaction_id, data);
                                    print(transaction_id);                                
                                    $("#bayar").modal('hide');
                                    cekstatus(transaction_id);
                                    fokus('barcode');
                                    bayarbill();
                                }, 200);                         
                                
                            });
                        }
                        function formatRupiah(num){
                            var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
                            if(str.indexOf(".") > 0) {
                                parts = str.split(".");
                                str = parts[0];
                            }
                            str = str.split("").reverse();
                            for(var j = 0, len = str.length; j < len; j++) {
                                if(str[j] != ",") {
                                output.push(str[j]);
                                if(i%3 == 0 && j < (len - 1)) {
                                    output.push(".");
                                }
                                i++;
                                }
                            }
                            formatted = output.reverse().join("");
                            return("" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
                        };
                        function closebayar(){
                            $("#bayar").hide();
                        }
                        function bayar(){
                            $("#bayar").modal();
                            fokus('bayar');
                            let tagihan = $("#tagihan").val();
                            $(".bill").html(formatRupiah(tagihan));  
                            cekpembayaran();
                        }
                        function cariproduk(){
                            let product_name=$("#cariproduk").val();
                            let type=$("#typesearch").val();
                            plistproduct(type,product_name);
                        }
                        function refresh(){
                            listnota(-1);
                            nota(0);
                            refreshlistproduct();
                        }
                        function refreshlistproduct(){
                            let typelist=$("#typesearch").val();
                            plistproduct(typelist,'');
                        }
                        function plistproduct(type,product_name){
                            cekprodukuniq();
                            let transaction_id=$("#transaction_id").val();
                            if(type=="gambar"){
                                // alert("<?=base_url("listproductgambar");?>?product_name="+product_name);
                                $.get("<?=base_url("listproductgambar");?>",{product_name:product_name})
                                .done(function(data1){
                                    setTimeout(function(){                                    
                                        $("#listproduct").html(data1);
                                        $("#typesearch").val("gambar");
                                        cekproductlanjutan(transaction_id);                                
                                    },100);
                                    
                                });
                            }
                            if(type=="list"){                                
                                // alert("<?=base_url("listproductlist");?>?product_name="+product_name);
                                $.get("<?=base_url("listproductlist");?>",{product_name:product_name})
                                .done(function(data2){
                                    setTimeout(function(){                                    
                                        $("#listproduct").html(data2);
                                        $("#typesearch").val("list");
                                        cekproductlanjutan(transaction_id);                                
                                    },100);
                                    
                                });
                            }  
                            // fokus('barcode');                          
                        }
                        function listnota(transaction_status){                           
                            let from =$("#from").val();
                            let to =$("#to").val();
                            if(transaction_status=='-1'){transaction_status=$("#listnotastatus").val();}
                            // alert("<?=base_url("listnota");?>?transaction_status="+transaction_status+"&from="+from+"&to="+to);
                            $.get("<?=base_url("listnota");?>",{transaction_status:transaction_status,from:from,to:to})
                            .done(function(data){
                                $("#listnota").html(data);
                                $("#listnotastatus").val(transaction_status);
                                if(transaction_status==0){$("#keterangan").html("Transaksi Sukses");}
                                if(transaction_status==2){$("#keterangan").html("Transaksi Pending");}
                            });
                        }
                        function nota(transaction_id){
                            // alert("<?=base_url("nota");?>?transaction_id="+transaction_id);
                            $.get("<?=base_url("nota");?>",{transaction_id:transaction_id})
                            .done(function(data){
                                $("#nota").html(data);
                                setTimeout(function(){                                    
                                    cekstatus(transaction_id);
                                    fokus('barcode');   
                                    cekproductlanjutan(transaction_id);                               
                                },100);
                            });
                        }
                        function createnota(){
                            let transaction_shift=$("#kasshift").val();
                            $.get("<?=base_url("createnota");?>",{transaction_shift:transaction_shift})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                nota(data);
                            });
                        }
                        function insertnotaproduk(){
                            let wajib = $("#wajib").val();
                            let arwajib = wajib.split(",");
                            let aman=1;
                            let nwajib ;          
                            let iwajib ; 
                            let y; 
                            let darwajib;  
                            let z; 

                            for(let x=1;x<arwajib.length;x++){
                                y = arwajib[x];                                
                                darwajib = y.split("=");
                                nwajib = darwajib[0];          
                                iwajib = darwajib[1];               

                                z = $("#"+iwajib).val();
                                // alert(z);
                                if(z==""||z==null){
                                    toast('Info', nwajib+' Harus Diisi!');
                                    aman=0;
                                }
                                // toast('Info', nwajib+' Harus Diisi!'+z);
                            }
                            
                            if(aman==1){
                                let product_id = $("#qtyproduct_id").val();
                                let transactiond_id = $("#transactiond_id").val();
                                let qty = $("#qtyproduct1").val();
                                if(transactiond_id>0){
                                    updateqty(transactiond_id,'update',qty);
                                }else{
                                    insertnotaqty();
                                }
                                
                                $("#jmlnota").modal("hide");
                                $("#ptamu").addClass("hide");
                                $("#pstart").addClass("hide");
                                $("#ptherapist").addClass("hide");
                                $("#qtyproduct").val(1);
                            }
                        }
                        function modalbackdrop(){
                                $(".modal-backdrop").attr("style","display: inline !important;");
                        }
                        function insertjmlnota(transactiond_id,qtyproduct,product_id,start,therapist,xstart,xtherapist,xfoc,xnominal,xpercent){
                            let wajib=$("#wajib").val();
                            if($("#transaction_id").val()>0){
                                $("#jmlnota").modal();
                                if(start==1){
                                    $("#ptamu").removeClass("hide");
                                    $("#pstart").removeClass("hide");
                                    $("#wajib").val(wajib+",Start=start");
                                    wajib=$("#wajib").val();
                                }else{
                                    $("#ptamu").addClass("hide");
                                    $("#pstart").addClass("hide");
                                    wajib = wajib.replace(",Start=start", "");
                                    $("#wajib").val(wajib);
                                    wajib=$("#wajib").val();                                    
                                }
                                if(therapist==1){
                                    $("#ptherapist").removeClass("hide");
                                    $("#wajib").val(wajib+",Therapist=xtherapist");
                                    wajib=$("#wajib").val();
                                }else{
                                    $("#ptherapist").addClass("hide");
                                    wajib = wajib.replace(",Therapist=xtherapist", "");
                                    $("#wajib").val(wajib);
                                    wajib=$("#wajib").val();
                                }
                                modalbackdrop();
                                $("#transactiond_id").val(transactiond_id);  
                                $("#qtyproduct_id").val(product_id);  
                                $("#qtyproduct").val(qtyproduct);  
                                $("#xstart").val(xstart);  
                                $("#xtherapist").val(xtherapist);  
                                $("#xfoc").val(xfoc);  
                                $("#xnominal").val(xnominal); 
                                $("#xpercent").val(xpercent);  
                                rupiahnumerik($("#qtyproduct"));
                                rupiahnumerik($("#xnominal"));
                                rupiahnumerik($("#xpercent"));
                                fokus('insertqty');    
                            }else{
                                toast('INFO >>>', 'Nota tidak ditemukan!');
                            }                     
                        }
                           
                        //masukin product hanya multi qty
                        function insertnotaqty(){
                            let transaction_id = $("#transaction_id").val();
                            let product_id = $("#qtyproduct_id").val();
                            let transactiond_qty = $("#qtyproduct").val();
                            let start = $("#start").val();
                            let xtherapist = $("#xtherapist").val();
                            let xfoc = $("#xfoc").val();
                            let xnominal = $("#xnominal1").val();
                            let xpercent = $("#xpercent1").val();
                            $("#transactiond_id").val(0);
                            // $("#test").html("<?=base_url("insertnota");?>?transaction_id="+transaction_id+"&product_id="+product_id+"&transactiond_qty="+transactiond_qty+"&start="+start+"&xtherapist="+xtherapist+"&xfoc="+xfoc+"&xnominal="+xnominal+"&xpercent="+xpercent);
                            $.get("<?=base_url("insertnota");?>",{transaction_id:transaction_id,product_id:product_id,transactiond_qty:transactiond_qty,start:start,xtherapist:xtherapist,xfoc:xfoc,xnominal:xnominal,xpercent:xpercent})
                            .done(function(data){
                                // alert(data);
                                setTimeout(() => {
                                    listnota($("#listnotastatus").val());
                                    nota(transaction_id);                                
                                    refreshlistproduct();                                
                                    $("#start").val("");
                                    cekproductlanjutan(transaction_id);                                    
                                }, 100);
                            });
                        } 
                        //masukin product hanya satu pcs
                       /*  function insertnota(product_id){
                            let transaction_id = $("#transaction_id").val();
                            let start = $("#start").val();
                            let therapist = $("#therapist").val();
                            let foc = $("#foc").val();
                            let nominal = $("#nominal").val();
                            let percent = $("#percent").val();
                            let customer_name = $("#customer_name").val();
                            alert("<?=base_url("insertnota");?>?transaction_id="+transaction_id+"&product_id="+product_id);
                            $.get("<?=base_url("insertnota");?>",{transaction_id:transaction_id,product_id:product_id})
                            .done(function(data){
                                listnota($("#listnotastatus").val());
                                nota(transaction_id);                                
                                refreshlistproduct();
                            });
                        } */
                        function insertnotabarcode(product_batch){

                            let transaction_id = $("#transaction_id").val();
                            $.get("<?=base_url("insertnota");?>",{transaction_id:transaction_id,product_batch:product_batch})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                nota(transaction_id);
                            });
                        }
                        function deletenota(transaction_id){
                            // alert("<?=base_url("deletenota");?>?transaction_id="+transaction_id);
                            let ok = confirm(' you want to delete?');
                            // alert(ok);
                            if(ok==true){
                                $.get("<?=base_url("deletenota");?>",{transaction_id:transaction_id})
                                .done(function(data){
                                    // alert(data);
                                    setTimeout(function(){ 
                                        listnota($("#listnotastatus").val());
                                        nota(transaction_id);
                                        refreshlistproduct();
                                        cekproductlanjutan(transaction_id); 
                                    },100);
                                });
                            }
                        }
                        function updatestatus(transaction_id, transaction_status){
                            // alert("<?=base_url("updatestatus");?>?transaction_id="+transaction_id+"&transaction_status="+transaction_status);                            
                            $.get("<?=base_url("updatestatus");?>",{transaction_id:transaction_id,transaction_status:transaction_status})
                            .done(function(data){
                                $("#status").val(transaction_status);
                                cekstatus(transaction_id);
                            });
                        }
                        function updateqty(transactiond_id, type, transactiond_qty){  
                            let start = $("#start").val();
                            let xtherapist = $("#xtherapist").val();
                            let xfoc = $("#xfoc").val();
                            let xnominal = $("#xnominal1").val();
                            let xpercent = $("#xpercent1").val();
                            let transaction_id = $("#transaction_id").val();
                            // $("#test").html("<?=base_url("updateqty");?>?transactiond_id="+transactiond_id+"&type="+type+"&transactiond_qty="+transactiond_qty+"&start="+start+"&xtherapist="+xtherapist+"&xfoc="+xfoc+"&xnominal="+xnominal+"&xpercent="+xpercent+"&transaction_id="+transaction_id);           
                            $.get("<?=base_url("updateqty");?>",{transactiond_id:transactiond_id,type:type,transactiond_qty:transactiond_qty,start:start,xtherapist:xtherapist,xfoc:xfoc,xnominal:xnominal,xpercent:xpercent,transaction_id:transaction_id})
                            .done(function(data){
                                // alert(data);
                                setTimeout(function(){                                    
                                    listnota($("#listnotastatus").val());
                                    nota(transaction_id);
                                    updatestatus(transaction_id, 2);
                                    refreshlistproduct();
                                    $("#start").val("");                                
                                    cekproductlanjutan(transaction_id);                             
                                },100);
                                
                            });
                        }

                        

                        function deletetransactiond(transaction_id,product_id,product_qty){
                            // alert("<?=base_url("deletetransactiond");?>?transactiond_id="+transactiond_id+"&product_id="+product_id+"&product_qty="+product_qty);
                            let ok = confirm(' you want to delete?');
                            // alert(ok);
                            if(ok==true){
                                $.get("<?=base_url("deletetransactiond");?>",{transaction_id:transaction_id,product_id:product_id,product_qty:product_qty})
                                .done(function(data){
                                    // alert(data);
                                    setTimeout(function(){                                    
                                        listnota($("#listnotastatus").val());
                                        nota(transaction_id);
                                        updatestatus(transaction_id, 2);
                                        refreshlistproduct();
                                        cekproductlanjutan(transaction_id);                         
                                    },100);
                                    
                                });
                            }
                        }
                        $(document).ready(function(){
                            listnota($("#listnotastatus").val());
                            plistproduct('gambar','');
                            closebayar();
                            modalstatus();
                            shift();
                            $('#showmember').on('hidden.bs.modal', function (e) {
                                fokus('barcode');
                            })
                        });
                        $(document).on("keyup", function(e){ 
                            let ifokus = $("#fokus").val();   
                            let transaction_id =   $("#transaction_id").val();  
                            
                            if (e.which==9) {
                                // alert(ifokus);
                                if(ifokus=="" || ifokus=="barcode"){
                                    fokus('cari');
                                }else if(ifokus=="cari"){
                                    fokus('barcode');
                                }else if(ifokus=="memberno"){
                                    fokus('membername');
                                }else if(ifokus=="membername"){
                                    fokus('memberno');
                                }
                            }else if(e.which=="13"){
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
                                ) {?>            
                                if(ifokus=="barcode"){
                                    let product_batch = $("#inputbarcode").val();
                                    if(product_batch==""){alert("Barcode tidak boleh kosong!");}
                                    insertnotabarcode(product_batch);
                                    $("#inputbarcode").val("");
                                }else if(ifokus=="bayar"){
                                    pelunasan();
                                }else if(ifokus=="modalawal"){
                                    kasmodal('masuk');
                                }else if(ifokus=="modalakhir"){
                                    kasmodal('keluar');
                                }else if(ifokus=="insertqty"){
                                    insertnotaproduk();
                                }   
                                <?php }?>  
                                
                            }else if(e.which==17){
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
                                ) {?>              
                                if(transaction_id>0){
                                    bayar();
                                }
                                <?php }?> 
                            }                                                   
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "Poin of Sale";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
    setTimeout(() => {
        $(".sidebartoggler").click();
        $(".page-titles").hide();
        $("#inputbarcode").focus();
    }, 300);
    setInterval(() => {
        let typelist=$("#typesearch").val();
        plistproduct(typelist,'');
        let transaction_id = $("#transaction_id").val();
        nota(transaction_id);
    }, 60000);
    
</script>

<?php echo  $this->include("template/footer_v"); ?>