<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NaverGameCrawl {
    public function __construct()
    {
        $this->ci =& get_instance();
    }

    //하루에 한번 돌리는걸로 가정함
    public function crawling($lastData) {
        $this->ci->load->library('SimpleHtmlDom');
        //$this->ci->load->library('simplehtmldom');

        if ( ! empty($lastData) && $lastData->created_date == date('Ymd')) {
            return [];
        }

        $targetYear = date('Y');
        $targetMonth = date('m');

        $q = rawurlencode("{$targetYear}년 {$targetMonth}월 모바일 출시 게임");
        $headerHtml =  file_get_html("https://search.naver.com/search.naver?sm=tab_hty.top&where=nexearch&query={$q}&oquery={$q}");
        $headerHtmlDom = $headerHtml->find('.gameinfo_tab');
        $headerDate = substr($headerHtmlDom[0]->find('._content', 0)->plaintext, 0, -1);
        $headerDate = explode('.', $headerDate);

        $jsonGameHtml =  file_get_html("https://search.naver.com/p/csearch/content/apirender.nhn?pkid=403&display=100&where=nexearch&os=&key=GameListAPI&so=s2.asc&_callback=&q={$q}&u1=");

        $oGameHtml = json_decode($jsonGameHtml);
        if ($oGameHtml->resultCode !== 'success') {
            return [];
        }

        foreach ($oGameHtml->result->item as $html) {
            $dom = str_get_html($html);
            $domGameList = $dom->find('.gameinfo_info');

            $info = [];
            foreach ($domGameList as $domGame) {
                $domInfo = $domGame->find('.info_text', 0);
                $appName = $domInfo->find('a', 0)->plaintext;
                $platform = $domInfo->find('.sub_cate', 0)->plaintext;

                $domThumb = $domGame->find('.info_thumb', 0);
                $imgUrl = $domThumb->find('img', 0)->src;

                $domDt = $domInfo->find('dt');
                $domDd = $domInfo->find('dd');
                $subInfo = [];
                foreach ($domDt as $idx => $dt) {
                    $category = trim($dt->plaintext);
                    $date = explode('~', $domDd[$idx]->plaintext);

                    $startDate = substr($date[0], 0, -1);
                    if (count($date) == 1) { //오픈
                        $endDate = null;
                    } else { //그외
                        $endDate = substr($date[1], 0, -1);
                    }

                    $subInfo[] = (object) [
                        'category'      => $category,
                        'startDate'     => str_replace('.', '-', $startDate),
                        'endDate'       => str_replace('.', '-', $endDate),
                    ];
                }

                $info[] = (object) [
                    'searchYear'    => $headerDate[0],
                    'searchDate'    => $headerDate[1],
                    'appName'       => $appName,
                    'platform'      => $platform == 'iOS' ? 'IOS' : 'AOS',
                    'imgUrl'        => $imgUrl,
                    'subInfo'       => $subInfo,
                ];
            }
            $resultList[] = $info;
        }
        return $resultList;
    }
}
