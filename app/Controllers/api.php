<?php

namespace App\Controllers;

use phpDocumentor\Reflection\Types\Null_;

class api extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }

    public function index()
    {
        echo "Page Not Found!";
    }

    public function active()
    {        
        $input["store_active"] = $this->request->getGET("store_active");
        $this->db->table('store')->update($input, array("store_id" => $this->request->getGET("store_id")));
        echo $this->db->getLastQuery();
    }

    public function createstore()
    {       
        //input store 
        $input["store_name"] = $this->request->getGET("store_name");
        $input["store_address"] = $this->request->getGET("store_address");
        $input["store_phone"] = $this->request->getGET("store_phone");
        $input["store_wa"] = $this->request->getGET("store_wa");
        $input["store_owner"] = $this->request->getGET("store_owner");
        $input["store_active"] = $this->request->getGET("store_active");
        $this->db->table('store')->insert($input);
        // echo $this->db->getLastQuery();
        $userid=$this->db->insertID();

        //input position
        $inputposition1["store_id"] = $userid;
        $inputposition1["position_name"] = "Admin";
        $inputposition2["position_administrator"] = 2;
        $this->db->table('position')->insert($inputposition1);
        $positionid1=$this->db->insertID();
        //input position
        $inputposition2["store_id"] = $userid;
        $inputposition2["position_administrator"] = 1;
        $inputposition2["position_name"] = "Administrator";
        $this->db->table('position')->insert($inputposition2);
        $positionid2=$this->db->insertID();

        //input user
        $inputuser1["store_id"] = $userid;
        $inputuser1["user_name"] = $this->request->getGET("user_name");
        $inputuser1["user_email "] = $this->request->getGET("user_email ");
        $inputuser1["user_password"] = password_hash($this->request->getGET("user_password"), PASSWORD_DEFAULT);
        $inputuser1["position_id"] = $positionid1;
        $this->db->table('user')->insert($inputuser1);

        //input user administrator
        $inputuser2["store_id"] = $userid;
        $inputuser2["user_name"] = "Administrator";
        $inputuser2["user_email "] = "ihsan.dulu@gmail.com";
        $inputuser2["user_password"] = "$2y$10$GjtRux7LHXpXN5JotL/J0uE1KyV5LQ.OQrapMZqbhHt84oB7WDoEa";
        $inputuser2["position_id"] = $positionid2;
        $this->db->table('user')->insert($inputuser2);
        echo $this->db->getLastQuery();

    }

    public function iswritable()
    {
        $dir = $_GET["path"];
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                echo "true";
            } else {
                echo "false";
            }
        } else if (file_exists($dir)) {
            return (is_writable($dir));
        }
    }

    public function updateakun()
    {
        $input["akun_id"] = $this->request->getGET("akun_id");
        if ($input["akun_id"] == 0) {
            $input["akun_id"] = null;
        }
        $this->db->table('lpj1')->update($input, array("lpj1_id" => $this->request->getGET("lpj1_id")));
        // echo $this->db->getLastQuery();
        $akun = $this->db->table("akun")
            ->where("akun.akun_id", $input["akun_id"])
            ->get();
        foreach ($akun->getResult() as $akun) {
            if ($input["akun_id"] > 0) {
                $isi = $akun->akun_name . " (" . $akun->akun_no . ")";
            } else {
                $isi = "";
            }
            echo $isi;
        }
    }

    public function akunkosong()
    {
        $akunkosong = $this->db->table("lpj1")
            ->where("lpj0_id", $this->request->getGET("lpj0_id"))
            ->groupStart()
            ->where("akun_id", null)
            ->orWhere("akun_id", "0")
            ->groupEnd()
            ->get()
            ->getNumRows();
        // echo $this->db->getLastQuery();
        if ($akunkosong == 0) {
            echo "OK";
        }
    }

    public function hakakses()
    {
        $crud = $this->request->getGET("crud");
        $val = $this->request->getGET("val");
        $val = json_decode($val);
        $position_id = $this->request->getGET("position_id");
        $pages_id = $this->request->getGET("pages_id");
        $where["position_id"]=$this->request->getGET("position_id");
        $where["pages_id"]=$this->request->getGET("pages_id");
        $cek=$this->db->table('positionpages')->where($where)->get()->getNumRows();
        if($cek>0){
            $input1[$crud] = $val;
            $this->db->table('positionpages')->update($input1, $where);
            echo $this->db->getLastQuery();
        }else{
            $input2["position_id"] = $position_id;
            $input2["pages_id"] = $pages_id;
            $input2[$crud] = $val;
            $this->db->table('positionpages')->insert($input2);
            echo $this->db->getLastQuery();
        }        
    }

    public function cekcost_approver()
    {

        $build = $this->db->table("cost_approver");
        if (isset($_GET["branch_id"])) {
            $build->where("branch_id", $this->request->getGet("branch_id"));
        }
        $usr = $build->get();
        echo $usr->getNumRows();
    }

    public function request0buttons()
    {
        $request0 = $this->db->table("request0")
            ->select("request0.request0_id, request0.request0_no, SUM(request1.request1_proposed_nom)AS nominal")
            ->join("request1", "request1.request0_id=request0.request0_id", "left")
            ->join("advance0", "advance0.request0_id=request0.request0_id", "left")
            ->whereIn("request0.branch_id", session()->branchrule, true)
            ->groupStart()
            ->where("advance0.request0_id IS NULL")
            ->orWhere("advance0.request0_id", $this->request->getGet("request0_id"))
            ->groupEnd()
            ->where("request0.request0_status", "Validated")
            ->groupBy("request1.request0_id")
            ->groupBy("request0.request0_id")
            ->get();
        //echo $this->db->getLastQuery();
        foreach ($request0->getResult() as $request0) { ?>
            <div class="col-md-2 p-2">
                <button id="b<?= $request0->request0_id; ?>" type="button" onclick="buka('<?= $request0->request0_id; ?>')" class="btn btn-warning pilihb">
                    <?= $request0->request0_no; ?> (Rp. <?= number_format($request0->nominal, 0, ",", "."); ?>)
                    <button onclick="batal()" id="bc<?= $request0->request0_id; ?>" type="button" class="btn btn-xs btn-danger fa fa-close btnclose" style="position: absolute; top:-3px; right:-5px;"></button>
                </button>
            </div>
        <?php }
    }

    public function request0detail()
    {
        ?>
        <div class="tabale-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover  table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr style="background-color:peachpuff;">
                        <th>Date of Filing</th>
                        <th>No.Req</th>
                        <th>Branch</th>
                        <th>Applicant</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $builder = $this->db
                        ->table("request0");

                    $builder->where("request0_id", $this->request->getGet("request0_id"));
                    $usr = $builder
                        ->join("user", "user.user_id=request0.user_id_created", "left")
                        ->join("branch", "branch.branch_id=request0.branch_id", "left")
                        ->orderBy("request0_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                    ?>
                        <tr id="d<?= $usr->request0_id; ?>">
                            <td><?= $usr->request0_date; ?></td>
                            <td><?= $usr->request0_no; ?></td>
                            <td><?= $usr->branch_name; ?></td>
                            <td><?= $usr->username; ?></td>
                            <td><?= $usr->request0_desc; ?></td>
                            <td><?= $usr->request0_status; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="table-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Cost</th>
                        <th>Proposed</th>
                        <th>Approved</th>
                        <th>Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usr = $this->db
                        ->table("request1")
                        ->join("cost", "cost.cost_id=request1.cost_id", "left")
                        ->where("request0_id", $this->request->getVar("request0_id"))
                        ->orderBy("request1_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                        $usr1 = $this->db
                            ->table("user")
                            ->join("contact", "contact.contact_id=user.contact_id", "left")
                            ->where("user_id", $usr->user_id_approved)
                            ->get();
                        /* echo $this->db->getLastquery();
                                        die; */
                        $user_name = "";
                        if ($usr1->getNumRows() > 0) {
                            $usr1 = $usr1->getRow();
                            $user_name = $usr1->contact_first_name;
                        }

                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $usr->created; ?></td>
                            <td><?= $usr->cost_name; ?></td>
                            <td><?= number_format($usr->request1_proposed_nom, 0, ",", "."); ?></td>
                            <td><?= number_format($usr->request1_approved_nom, 0, ",", "."); ?></td>
                            <td><?= ($user_name == "") ? "" : $user_name . " (" . $usr->approved . ")"; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php
    }

    public function advance0detail()
    {
    ?>
        <div class="tabale-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover  table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr style="background-color:peachpuff;">
                        <th>Action</th>
                        <th>Date of Filing</th>
                        <th>No.Advance</th>
                        <th>No.LPJ</th>
                        <th>Branch</th>
                        <th>Applicant</th>
                        <th>Total Nominal</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $builder = $this->db
                        ->table("advance0");
                    $builder->where("advance0.advance0_id", $this->request->getGet("advance0_id"));
                    $usr = $builder
                        ->select("*, advance0.advance0_id AS advance0_id, advance0.user_id_approved AS user_id_approved, advance0.approved AS approved")
                        ->join("(SELECT advance0_id AS advance0d_id, SUM(advance1_nom)AS dnom FROM advance1 GROUP BY advance0_id)advance1", "advance1.advance0d_id=advance0.advance0_id", "left")
                        ->join("user", "user.user_id=advance0.user_id_created", "left")
                        ->join("request0", "request0.request0_id=advance0.request0_id", "left")
                        ->join("(SELECT request0_id AS request0d_id,SUM(request1_proposed_nom)AS proposenom, SUM(request1_approved_nom)AS approvenom FROM request1 GROUP BY request0_id)request1", "request1.request0d_id=request0.request0_id", "left")
                        ->join("branch", "branch.branch_id=request0.branch_id", "left")
                        ->join("lpj0", "lpj0.advance0_id=advance0.advance0_id", "left")
                        ->orderBy("advance0.advance0_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                    ?>
                        <tr id="d<?= $usr->advance0_id; ?>">
                            <td style="padding-left:0px; padding-right:0px;">
                                <form method="post" class="btn-action" style="">
                                    <a href="#" onclick="tampilrow(this,'d<?= $usr->advance0_id; ?>','9', ['Advance Approved Date','Request Number','Request Proposed', 'Request Approved'], ['<?= $usr->approved; ?>', '<?= $usr->request0_no; ?>', '<?= number_format($usr->proposenom, 0, ",", "."); ?>', '<?= number_format($usr->approvenom, 0, ",", "."); ?>'])" class="btn btn-sm btn-success tampilrow">
                                        <span class="fa fa-plus" style="color:white;"></span>
                                    </a>
                                </form>
                            </td>
                            <td><?= $usr->advance0_date; ?></td>
                            <td><?= $usr->advance0_no; ?></td>
                            <td><?= $usr->lpj0_no; ?></td>
                            <td><?= $usr->branch_name; ?></td>
                            <td><?= $usr->username; ?></td>
                            <td><?= number_format($usr->advance0_nom, 0, ",", "."); ?></td>
                            <td><?= $usr->advance0_desc; ?></td>
                            <td><?= $usr->advance0_status; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="table-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Cash</th>
                        <th>Nominal</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usr = $this->db
                        ->table("advance1")
                        ->join("cash", "cash.cash_id=advance1.cash_id", "left")
                        ->where("advance0_id", $this->request->getVar("advance0_id"))
                        ->orderBy("advance1_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date("Y-m-d", strtotime($usr->created)); ?></td>
                            <td><?= $usr->cash_name; ?></td>
                            <td><?= number_format($usr->advance1_nom, 0, ",", "."); ?></td>
                            <td><?= $usr->advance1_bukti; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
<?php
    }

    public function listprepaid_prepaidpayment(){
        $prepaid_id=$this->request->getGET("prepaid_id");        
        $prepaid = $this->db->table("prepaid")
        ->where("branch_id", $this->request->getGET("branch_id"))
        ->get(); 
        // echo $this->db->getLastQuery();
        ?>
        <option value="0" <?= ($prepaid_id == "0") ? "selected" : ""; ?>>Select Prepaid</option>                                            
       <?php
        foreach ($prepaid->getResult() as $prepaid) { ?>
            <option value="<?= $prepaid->prepaid_id; ?>" <?= ($prepaid_id == $prepaid->prepaid_id) ? "selected" : ""; ?>><?= $prepaid->prepaid_name; ?></option>
        <?php } ?>
    <?php }

    public function listcash_prepaidpayment(){
        $cash_id=$this->request->getGET("cash_id");        
        $cash = $this->db->table("cash")
        ->where("branch_id", $this->request->getGET("branch_id"))
        ->get(); 
        echo $this->db->getLastQuery();
        ?>
        <option value="0" <?= ($cash_id == "0") ? "selected" : ""; ?>>Select Cash</option>                                            
       <?php
        foreach ($cash->getResult() as $cash) { ?>
            <option value="<?= $cash->cash_id; ?>" <?= ($cash_id == $cash->cash_id) ? "selected" : ""; ?>><?= $cash->cash_name; ?></option>
        <?php } ?>        
    <?php }

    public function cekprodukuniq(){
        $product=$this->db->table("product")
        ->where("product_end <",date("Y-m-d H:i:s"))
        ->get();
        foreach($product->getResult() as $product){
            // echo $product->product_name."<br/>";
            $input["product_start"]=null;
            $input["product_bend"]=null;
            $input["product_end"]=null;
            $input["transaction_id"]="0";
            $input["customer_name"]="";
            $where["product_id"]=$product->product_id;
            $this->db->table("product")
            ->update($input,$where);
        }
    }

    public function upload(){
    // $server="https://server/public/";
    $server=base_url("/");
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <?php
        $transaction=$this->db->table("transaction")
        ->where("transaction_upload","0")
        ->get();
        foreach($transaction->getResult() as $transaction){
            $url=$server."api/tupload?";
            $parameter="";
            $no=0;
           foreach ($this->db->getFieldNames('transaction') as $field) {
                if($no==0){$dan="";$koma="";}else{$dan="&";$koma=",";}
                $url.=$dan.$field."=".$transaction->$field;
                $parameter.=$koma.$field.":".$transaction->$field;
                $no++;
            }
            echo $url."<br/>";
            ?>
            <script>
                $.get("<?=$server;?>api/tupload",{<?=$parameter;?>})
                .done(function(data){
                    if(data==1){
                        $.get("<?=$server;?>/updatetransaction",{transaction_upload:data,transaction_id:<?=$transaction->transaction_id;?>})
                        .done(function(data){

                        });
                    }
                });
            </script>
            <?php
        }
    }

    public function tupload(){
        foreach ($this->request->getGet() as $e => $f) {
            if ($e != 'create') {
                $input[$e] = $this->request->getGet($e);
            }
        }

        $builder = $this->db->table('transaction');
        $builder->insert($input);
        if($builder){
            echo "1";
        }else{
            echo "0";
        }
        /* echo $this->db->getLastQuery();
        die; */
    }

    public function updatetransaction(){
        foreach ($this->request->getGet() as $e => $f) {
            if ($e != 'transaction_id') {
                $input[$e] = $this->request->getGet($e);
            }
        }

        $where["transaction_id"] = $this->request->getGet("transaction_id");
        $builder = $this->db->table('transaction');
        $builder->update($input,$where);
        if($builder){
            echo "1";
        }else{
            echo "0";
        }
        /* echo $this->db->getLastQuery();
        die; */
    }

    public function urutan(){
        $builder = $this->db->table('product');
        if(isset($_GET["category_id"])){
            $builder->where("category_id",$_GET["category_id"]);
        }
        $builder->orderBy("product_urutan","DESC");
        $builder->limit("1");
        $urutan=$builder->get();
        $urutanterakhir=1;
        foreach($urutan->getResult() as $urutan){
            $urutanterakhir=$urutan->product_urutan+1;
        }
        echo $urutanterakhir;
        /* echo $this->db->getLastQuery();
        die; */
    }
}
