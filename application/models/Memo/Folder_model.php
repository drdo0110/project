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

    public function getUniqueFolderRow($aWhere) {
        $selectQuery = $this->db
            ->select('
                folder.name AS folderName
            ')
            ->from('folder')
            ->where('name', $aWhere['name'])
            ->get();

        return $selectQuery->row();
    }
}
