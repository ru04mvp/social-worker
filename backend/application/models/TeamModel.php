<?php
class TeamModel extends CI_Model {

    public $db;

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

}