<?php

namespace App\Models\master;

use App\Models\core_m;

class mloker_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek loker
        if ($this->request->getVar("loker_id")) {
            $lokerd["loker_id"] = $this->request->getVar("loker_id");
        } else {
            $lokerd["loker_id"] = -1;
        }
            $lokerd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("loker")
            ->getWhere($lokerd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "loker_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $loker) {
                foreach ($this->db->getFieldNames('loker') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $loker->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('loker') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $loker_id=   $this->request->getPost("loker_id");
            $cek=$this->db->table("product")
            ->where("loker_id", $loker_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "loker masih dipakai di data product!";
            } else{            
                $this->db
                ->table("loker")
                ->delete(array("loker_id" => $this->request->getPost("loker_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'loker_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('loker');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $loker_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'loker_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('loker')->update($input, array("loker_id" => $this->request->getPost("loker_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
