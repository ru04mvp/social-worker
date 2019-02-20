<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        #載入必Load項目
        $this->load->helper("Common_Helper");
    }

    /**
     * 返回系統線上狀態
     *
     * @return void
     */
    public function index() {
        json_respon(['code' => '0000', 'message' => 'system is work.']);
    }
}