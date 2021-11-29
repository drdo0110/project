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
        $lastData = $this->naverGame->getLastData(); //model

        $this->load->library('NaverGameCrawl');
        $naverGameCrawList = $this->navergamecrawl->crawling($lastData); //lib

        //db insert
        if (count($naverGameCrawList) > 0) {
            $this->naverGame->setCrawlingData($naverGameCrawList); //model
        }
    }

    public function naverGameList() {
        $naverGameList = $this->naverGame->getNaverGameList(); //model
        print_r($naverGameList);
    }
}
