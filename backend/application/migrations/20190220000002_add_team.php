<?php
defined("BASEPATH") OR exit("No direct script access allowed");
// Migration 應用語法自動化產生 - 2018-08-02 00:18:09 Power By Jake@jbravo.com.tw
class Migration_Add_sessions extends CI_Migration {
    public function up() { // 設定資料表欄位
        $this->dbforge->add_field(array(
            'id' => array('type' => 'varchar', 'CONSTRAINT' => '40', 'comment' => '', 'default' => '0'),
            'ip_address' => array('type' => 'varchar', 'CONSTRAINT' => '45', 'comment' => '', 'default' => '0', 'null' => true),
            'user_agent' => array('type' => 'varchar', 'CONSTRAINT' => '120', 'comment' => '', 'null' => true),
            'last_activity' => array('type' => 'int', 'comment' => '', 'default' => '0', 'null' => true),
            'user_data' => array('type' => 'text', 'comment' => '', 'null' => true),
            'timestamp' => array('type' => 'int', 'comment' => '', 'null' => true),
            'data' => array('type' => 'text', 'comment' => '', 'null' => true),
        ));
        // 設定資料表key值
        $this->dbforge->add_key("id", TRUE);
        // 建立資料表
        $this->dbforge->create_table("sessions");
        // 建立預設內容 (自動重新建立資料表時可自訂預設內容)
        ####################################################
        ##
        ##
        ##
    }
    public function down() {
        // 移除資料表
        $this->dbforge->drop_table("sessions");
    }
}
