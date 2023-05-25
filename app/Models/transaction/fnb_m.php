<?php

namespace App\Models\transaction;

use App\Models\core_m;

class fnb_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek fnb
        if ($this->request->getVar("fnb_id")) {
            $fnbd["fnb_id"] = $this->request->getVar("fnb_id");
        } else {
            $fnbd["fnb_id"] = -1;
        }
            $fnbd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("fnb")
            ->getWhere($fnbd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "fnb_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $fnb) {
                foreach ($this->db->getFieldNames('fnb') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $fnb->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('fnb') as $field) {
                $data[$field] = "";
            }
            $data["fnb_ppn"] = "0";
            $data["fnb_date"] = date("Y-m-d");
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $fnb_id=   $this->request->getPost("fnb_id");
                     
            $this->db
            ->table("fnb")
            ->delete(array("fnb_id" => $this->request->getPost("fnb_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'fnb_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            /* if(isset($_POST["fnb_no"])&&$_POST["fnb_no"]!=""){
                $input["fnb_no"] = $_POST["fnb_no"];
            }else{
                $input["fnb_no"] = "PUR".date("YmdHis").session()->get("store_id");
            } */

            $builder = $this->db->table('fnb');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $fnb_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

           
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'fnb_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('fnb')->update($input, array("fnb_id" => $this->request->getPost("fnb_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;

                        
              
            
        }
        return $data;
    }
}
