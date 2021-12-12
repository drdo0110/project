<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    public function getFileList() {
        $selectQuery = $this->db
            ->select('
                *
            ')
            ->from('file')
            ->get();

        return $selectQuery->result();
    }

    public function getFileRow($seq) {
        $selectQuery = $this->db
            ->select('
                *
            ')
            ->from('file')
            ->where('seq', $seq)
            ->get();

        return $selectQuery->row();
    }

    public function setFile($data) {
        $this->db->insert('file', $data);
        return $this->db->insert_id();
    }

    public function removeFile($data) {
        $this->db->delete('file', $data);
        return true;
    }
}
