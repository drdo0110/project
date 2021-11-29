<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('cookie');
    }

    protected function useProfile() {
        $this->output->enable_profiler(TRUE);
    }
}
