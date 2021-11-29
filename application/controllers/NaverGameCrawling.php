<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NaverGameCrawling extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Crawling/Naver_game_model', 'naverGame');
    }

    public function index() {
        $this->crawlingData();
        $this->naverGameList();
    }

    public function crawlingData() {
        $lastData = $this->naverGame->getLastData();

        $this->load->library('NaverGame');
        $naverGameCrawList = $this->NaverGame->crawling($lastData);

        //db insert
        if (count($naverGameCrawList) > 0) {
            $this->NaverGame->setCrawlingData($naverGameCrawList);
        }
    }

    public function naverGameList() {
        $naverGameList = $this->NaverGame->getNaverGameList();
        print_r($naverGameList);
    }
}
