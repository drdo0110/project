<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_join_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    public function setUserJoin($data) {
        $this->db->insert('user_join', $data);
    }
}
