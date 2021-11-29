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

        $this->load->library('naver_game');
        $naverGameCrawList = $this->naver_game->crawling($lastData);

        //db insert
        if (count($naverGameCrawList) > 0) {
            $this->naver_game->setCrawlingData($naverGameCrawList);
        }
    }

    public function naverGameList() {
        $naverGameList = $this->naver_game->getNaverGameList();
        print_r($naverGameList);
    }
}
