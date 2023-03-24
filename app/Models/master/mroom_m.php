<?php

namespace App\Models\master;

use App\Models\core_m;

class mroom_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek room
        if ($this->request->getVar("room_id")) {
            $roomd["room_id"] = $this->request->getVar("room_id");
        } else {
            $roomd["room_id"] = -1;
        }
            $roomd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("room")
            ->getWhere($roomd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "room_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $room) {
                foreach ($this->db->getFieldNames('room') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $room->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('room') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $room_id=   $this->request->getPost("room_id");
            $cek=$this->db->table("product")
            ->where("room_id", $room_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "room masih dipakai di data product!";
            } else{            
                $this->db
                ->table("room")
                ->delete(array("room_id" => $this->request->getPost("room_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'room_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('room');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $room_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'room_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('room')->update($input, array("room_id" => $this->request->getPost("room_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
