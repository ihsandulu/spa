<?php

namespace App\Models\master;

use App\Models\core_m;

class mmetodepembayaran_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek metodepembayaran
        if ($this->request->getVar("metodepembayaran_id")) {
            $metodepembayarand["metodepembayaran_id"] = $this->request->getVar("metodepembayaran_id");
        } else {
            $metodepembayarand["metodepembayaran_id"] = -1;
        }
            $metodepembayarand["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("metodepembayaran")
            ->getWhere($metodepembayarand);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "metodepembayaran_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $metodepembayaran) {
                foreach ($this->db->getFieldNames('metodepembayaran') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $metodepembayaran->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('metodepembayaran') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $metodepembayaran_id=   $this->request->getPost("metodepembayaran_id");
            $this->db
            ->table("metodepembayaran")
            ->delete(array("metodepembayaran_id" => $this->request->getPost("metodepembayaran_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'metodepembayaran_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('metodepembayaran');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $metodepembayaran_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'metodepembayaran_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('metodepembayaran')->update($input, array("metodepembayaran_id" => $this->request->getPost("metodepembayaran_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
