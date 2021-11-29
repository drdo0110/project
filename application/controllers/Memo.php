<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Memo extends MY_Controller {
    const VIEW_PATH = 'memo';

    function __construct()
    {
        parent::__construct();
        $this->load->model('Memo/Folder_list_model', 'folderList');
        $this->load->model('Memo/File_list_model', 'fileList');
    }

    public function index() {
        $data['fileList'] = $this->_getFileList();
        $data['folderList'] = $this->_getFolderList();

        echo $this->_content($data);
    }

    private function _getFileList() {
        return $this->fileList->getFileList();
    }

    private function _getFolderList() {
        return $this->folderList->getFolderList();
    }

    public function addFile() {
        $oPost = (object) $this->input->post(null, true);
        $data = [
            'parent_id' => $oPost->parent_id,
            'name'      => $oPost->file_name
        ];

        $result_seq = $this->fileList->setFile($data);
        if ( ! empty($result_seq)) {
            $fileList = $this->fileList->getFileRow($result_seq);
            echo json_encode($fileList);
        }
    }

    public function removeFile() {
        $oPost = (object) $this->input->post(null, true);
        $data = [
            'seq'   => $oPost->seq
        ];

        $result = $this->fileList->removeFile($data);
        echo $result;
    }

    private function _content($data) {
       $this->load->view(self::VIEW_PATH . '/contents', $data);
    }
}
