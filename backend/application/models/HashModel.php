<?php
use Hashids\Hashids;

class HashModel extends CI_Model {

    public $db;
    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

    /**
     * 處理團隊ID
     *
     * @return void
     */
    public function getTeamId($id = 0, $opt = 1) {
        $hashid = new Hashids('hash_team_id');
        if ($opt == 1) {
            return $hashid->encode([$id, 0, rand(0, 999999)]);
        }
        if ($opt == 2) {
            return $hashid->decode([$id, 0, rand(0, 999999)]);
        }
        return false;
    }

    /**
     * 處理頻道ID
     *
     * @return void
     */
    public function getChannelId($id = 0, $opt = 1) {

    }

    /**
     * 處理會員ID
     *
     * @return void
     */
    public function getMemberId($id = 0, $opt = 1) {

    }

    /**
     * 處理訊息ID
     *
     * @return void
     */
    public function getMessageId($id = 0, $opt = 1) {

    }
}