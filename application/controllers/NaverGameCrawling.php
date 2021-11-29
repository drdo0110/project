<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NaverGameCrawling extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('crawling/naver_game_model', 'naverGame');
    }

    public function index() {
        $this->crawlingData();
        $this->naverGameList();
    }

    public function crawlingData() {
        $lastData = $this->naverGame->getLastData();

        $this->load->library('navergame');
        $naverGameCrawList = $this->navergame->crawling($lastData);

        //db insert
        if (count($naverGameCrawList) > 0) {
            $this->naverGame->setCrawlingData($naverGameCrawList);
        }
    }

    public function naverGameList() {
        $naverGameList = $this->naverGame->getNaverGameList();
        print_r($naverGameList);
    }
}
