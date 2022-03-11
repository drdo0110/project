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

    public function getUniqueFileRow($data) {
        $selectQuery = $this->db
            ->select('
                file.name AS fileName,
                folder.name AS folderName
            ')
            ->from('file AS file')
            ->where('file.parent_id', $data['parent_id'])
            ->where('file.name', $data['name'])
            ->join('folder AS folder', 'folder.seq = file.parent_id')
            ->get();

        return $selectQuery->row();
    }

    //신규파일
    public function setFile($data) {
        $this->db->insert('file', $data);
        return $this->db->insert_id();
    }

    //파일삭제
    public function removeFile($data) {
        $this->db->delete('file', $data);
        return true;
    }

    public function changeRenameFile($aUpdate) {
        return $this->db->update('file', $aUpdate['set'], $aUpdate['where']);
    }
}
