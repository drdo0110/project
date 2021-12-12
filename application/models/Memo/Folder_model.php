<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Folder_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    public function getFolderList() {
        $selectQuery = $this->db
            ->select('
                *
            ')
            ->from('folder')
            ->get();

        return $selectQuery->result();
    }
}
