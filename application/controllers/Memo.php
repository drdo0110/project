<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Memo extends MY_Controller {
    const VIEW_PATH = 'memo';

    function __construct()
    {
        parent::__construct();
        $this->load->model('Memo/Folder_model', 'folder');
        $this->load->model('Memo/File_model', 'file');
        $this->load->model('User_join_model', 'userJoin');
    }

    public function index() {
        $userJoinData = [
            'link'  => ''
        ];
        $this->userJoin->setUserJoin($userJoinData);

        $data['fileList'] = json_encode($this->_loadFileList());
        $data['folderList'] = $this->_loadFolderList();

        echo $this->_content($data);
    }

    //파일 리스트 호출
    private function _loadFileList() {
        return $this->file->getFileList();
    }

    //폴더 리스트 호출
    private function _loadFolderList() {
        return (object) [
            'main'  => $this->folder->getMainFolderList(),
            'sub'   => json_encode($this->folder->getSubFolderList())
        ];
    }

    //파일 row 호출
    private function _loadCommonFileRow($seq) {
        if (empty($seq)) {
            return false;
        }

        return $this->file->getFileRow($seq);
    }

    //폴더 row 호출
    private function _loadCommonFolderRow($seq) {
        if (empty($seq)) {
            return false;
        }

        return $this->folder->getFolderRow($seq);
    }

    //파일 선택시 호출
    public function loadFileRow() {
        $oGet = (object) $this->input->get(null, true);

        if ( ! empty($oGet->seq)) {
            $fileRow = $this->_loadCommonFileRow($oGet->seq);
            echo json_encode($fileRow);
        }
    }

    //폴더 선택시 호출
    public function loadFolderRow() {
        $oGet = (object) $this->input->get(null, true);

        if ( ! empty($oGet->seq)) {
            $folderRow = $this->_loadCommonFolderRow($oGet->seq);
            echo json_encode($folderRow);
        }
    }

    //폴더 생성
    public function addFolder() {
        $oPost = (object) $this->input->post(null, true);
        $data = [
            'parent_id' => ! empty($oPost->parentId) ? $oPost->parentId : 0,
            'name'      => $oPost->folderName
        ];

        $uniqueFolder = $this->folder->getUniqueFolderRow($data);
        if ( ! empty($uniqueFolder)) {
            $status = false;
            $msg = "{$uniqueFolder->folderName} 폴더 아래에 이미 존재합니다.";

            $result = [
                'status'    => $status,
                'msg'       => $msg
            ];

            echo json_encode($result);
            return false;
        }

        $result_seq = $this->folder->setFolder($data);
        if ( ! empty($result_seq)) {
            //생성된 파일 바로 뿌려주기 위한 select
            $folderRow = $this->_loadCommonFolderRow($result_seq);
            $folderRow->status = true;

            echo json_encode($folderRow);
        }
    }

    //파일 생성
    public function addFile() {
        $oPost = (object) $this->input->post(null, true);
        $data = [
            'parent_id' => $oPost->parentId,
            'name'      => $oPost->fileName
        ];

        $uniqueFile = $this->file->getUniqueFileRow($data);
        if ( ! empty($uniqueFile)) {
            $status = false;
            $msg = "{$uniqueFile->folderName} 폴더 아래에 {$uniqueFile->fileName} 파일이 이미 존재합니다.";

            $result = [
                'status'    => $status,
                'msg'       => $msg
            ];

            echo json_encode($result);
            return false;
        }

        $result_seq = $this->file->setFile($data);
        if ( ! empty($result_seq)) {
            //생성된 파일 바로 뿌려주기 위한 select
            $fileRow = $this->_loadCommonFileRow($result_seq);
            $fileRow->status = true;

            echo json_encode($fileRow);
        }
    }

    //폴더 삭제
    public function removeFolder() {
        $oPost = (object) $this->input->post(null, true);

        $data['seq'] = $oPost->seq;

        $result = false;
        $result = $this->folder->removeFolder($data);
        if ($result) {
            unset($data['seq']);
            $data['parent_id'] = $oPost->seq;
            $result = $this->file->removeFile($data);
        }
        echo $result;
    }

    //파일 삭제
    public function removeFile() {
        $oPost = (object) $this->input->post(null, true);

        $data = [
            'seq'   => $oPost->seq
        ];

        $result = false;
        $result = $this->file->removeFile($data);
        echo $result;
    }

    //이름 변경
    public function changeRename() {
        $oGet = (object) $this->input->get(null, true);
        $status = true;

        $aWhere = [
            'parent_id'   => $oGet->folderSeq,
            'name'        => $oGet->changeName
        ];

        if ($oGet->type == 'file') {
            $uniqueFile = $this->file->getUniqueFileRow($aWhere);
            if ( ! empty($uniqueFile)) {
                $status = false;
                $msg = "{$uniqueFile->folderName} 폴더 아래에 {$uniqueFile->fileName} 파일이 이미 존재합니다.";
            } else {
                //업데이트
                $aUpdate = [
                    'set'   => [
                        'name'  => $oGet->changeName
                    ],
                    'where' => [
                        'parent_id' => $oGet->folderSeq,
                        'seq'       => $oGet->fileSeq
                    ]
                ];

                $updateResult = $this->file->changeRenameFile($aUpdate);
                $msg = "파일 이름 변경 완료";
            }
        } else {
            $uniqueFolder = $this->folder->getUniqueFolderRow($aWhere);
            if ( ! empty($uniqueFolder)) {
                $status = false;
                $msg = "{$uniqueFolder->folderName} 폴더가 이미 존재합니다.";
            } else {
                //업데이트
            }
        }

        $result = [
            'status'    => $status,
            'msg'       => $msg
        ];

        echo json_encode($result);
    }

    //view
    private function _content($data) {
       $this->load->view(self::VIEW_PATH . '/contents', $data);
    }
}
