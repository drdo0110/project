<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Naver_game_model extends MY_Model {

    function __construct()
    {
        parent::__construct();
    }

    //마지막 데이터 단일로 select
    public function getLastData() {
        $selectQuery = $this->db
            ->select('
                app_name,
                date_format(created_date, "%Y%m%d") AS created_date
            ')
            ->from('naver_game_crawling')
            ->order_by('created_date DESC')
            ->get();

        if ( ! empty($selectQuery)) {
            return $selectQuery->row();
        }
    }

    //list select
    public function getNaverGameList() {
        $selectQuery = $this->db
            ->select('
                search_year AS searchYear,
                search_date AS searchDate,
                app_name AS appName,
                category AS category,
                start_date AS startDate,
                end_date AS endDate,
                app_name AS appName,
                platform AS platform
            ')
            ->from('naver_game_crawling')
            ->order_by('search_year ASC, search_date ASC, app_name ASC')
            ->get();

        if ( ! empty($selectQuery)) {
            return $selectQuery->result();
        }
    }

    //크롤링 데이터 insert
    public function setCrawlingData($resultList) {
        foreach ($resultList as $result) {
            $setData = [];
            foreach ($result as $idx => $info) {
                $setData = [
                    'search_year'   => $info->searchYear,
                    'search_date'   => $info->searchDate,
                    'app_name'      => $info->appName,
                    'platform'      => $info->platform,
                    'img_url'       => $info->imgUrl,
                ];

                foreach ($info->subInfo as $subInfo) {
                    $setData['category'] = $subInfo->category;
                    $setData['start_date'] = $subInfo->startDate;
                    $setData['end_date'] = $subInfo->endDate;

                    $this->insertIgnore('naver_game_crawling', $setData);
                }
            }
        }
    }
}
