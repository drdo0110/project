<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Folder_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    public function getMainFolderList() {
        $selectQuery = $this->db
            ->select('
                *
            ')
            ->where('parent_id', null)
            ->from('folder')
            ->get();

        return $selectQuery->result();
    }

    public function getSubFolderList() {
        $selectQuery = $this->db
            ->select('
                folder.seq AS folderSeq,
                folder.parent_id AS folderParentId,
                folder.name AS folderName
            ')
            ->where('folder.parent_id !=', null)
            ->from('folder')
            ->get();

        return $selectQuery->result();
    }

    public function getFolderRow($seq) {
        $selectQuery = $this->db
            ->select('
                *
            ')
            ->from('folder')
            ->where('seq', $seq)
            ->get();

        return $selectQuery->row();
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

    //신규폴더
    public function setFolder($data) {
        $this->db->insert('folder', $data);
        return $this->db->insert_id();
    }
}
