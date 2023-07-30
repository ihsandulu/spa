<?php

namespace App\Models\master;

use App\Models\core_m;

class mmetodepembayarand_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek metodepembayarand
        if ($this->request->getVar("metodepembayarand_id")) {
            $metodepembayarandd["metodepembayarand_id"] = $this->request->getVar("metodepembayarand_id");
        } else {
            $metodepembayarandd["metodepembayarand_id"] = -1;
        }
            $metodepembayarandd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("metodepembayarand")
            ->getWhere($metodepembayarandd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "metodepembayarand_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $metodepembayarand) {
                foreach ($this->db->getFieldNames('metodepembayarand') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $metodepembayarand->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('metodepembayarand') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $metodepembayarand_id=   $this->request->getPost("metodepembayarand_id");
            $this->db
            ->table("metodepembayarand")
            ->delete(array("metodepembayarand_id" => $this->request->getPost("metodepembayarand_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'metodepembayarand_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('metodepembayarand');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $metodepembayarand_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'metodepembayarand_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('metodepembayarand')->update($input, array("metodepembayarand_id" => $this->request->getPost("metodepembayarand_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
