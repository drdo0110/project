<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NaverGameCrawling extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Crawling/Naver_game_model', 'naverGameCrawling');
    }

    public function index() {
        $this->crawlingData();
        $this->naverGameList();
    }

    public function crawlingData() {
        $lastData = $this->naverGameCrawling->getLastData();

        $this->load->library('Naver_Game');
        $naverGameCrawList = $this->Naver_Game->crawling($lastData);

        //db insert
        if (count($naverGameCrawList) > 0) {
            $this->Naver_Game->setCrawlingData($naverGameCrawList);
        }
    }

    public function naverGameList() {
        $naverGameList = $this->Naver_Game->getNaverGameList();
        print_r($naverGameList);
    }
}
