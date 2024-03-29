<?php

namespace App\Models\master;

use App\Models\core_m;

class mmastermetodepembayaran_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek mastermetodepembayaran
        if ($this->request->getVar("mastermetodepembayaran_id")) {
            $mastermetodepembayarand["mastermetodepembayaran_id"] = $this->request->getVar("mastermetodepembayaran_id");
        } else {
            $mastermetodepembayarand["mastermetodepembayaran_id"] = -1;
        }
            $mastermetodepembayarand["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("mastermetodepembayaran")
            ->getWhere($mastermetodepembayarand);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "mastermetodepembayaran_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $mastermetodepembayaran) {
                foreach ($this->db->getFieldNames('mastermetodepembayaran') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $mastermetodepembayaran->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('mastermetodepembayaran') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $mastermetodepembayaran_id=   $this->request->getPost("mastermetodepembayaran_id");
            $this->db
            ->table("mastermetodepembayaran")
            ->delete(array("mastermetodepembayaran_id" => $this->request->getPost("mastermetodepembayaran_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'mastermetodepembayaran_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('mastermetodepembayaran');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $mastermetodepembayaran_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'mastermetodepembayaran_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('mastermetodepembayaran')->update($input, array("mastermetodepembayaran_id" => $this->request->getPost("mastermetodepembayaran_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
