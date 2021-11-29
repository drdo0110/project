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

        $this->load->library('naverGame');
        $naverGameCrawList = $this->navergame->crawling($lastData);

        //db insert
        if (count($naverGameCrawList) > 0) {
            $this->navergame->setCrawlingData($naverGameCrawList);
        }
    }

    public function naverGameList() {
        $naverGameList = $this->navergame->getNaverGameList();
        print_r($naverGameList);
    }
}
