<?php
defined("BASEPATH") OR exit("No direct script access allowed");
// Migration 應用語法自動化產生 - 2018-08-02 00:18:09 Power By Jake@jbravo.com.tw
class Migration_Add_team extends CI_Migration {
    public function up() { // 設定資料表欄位
        $this->dbforge->add_field(array(
            'id' => array('type' => 'int', 'comment' => '', 'auto_increment' => TRUE),
            'name' => array('type' => 'varchar', 'CONSTRAINT' => '200', 'comment' => '團體名稱', 'default' => '0', 'null' => true),
            'directions' => array('type' => 'varchar', 'CONSTRAINT' => '2000', 'comment' => '團體簡述', 'default' => '0', 'null' => true),
            'main_type' => array('type' => 'int', 'comment' => '主要類型', 'default' => '0', 'null' => true),
            'minor_type' => array('type' => 'int', 'comment' => '次要類型', 'default' => '0', 'null' => true),
            'api_token' => array('type' => 'varchar', 'CONSTRAINT' => '100', 'comment' => 'Token', 'default' => '0', 'null' => true),
            'api_secret' => array('type' => 'varchar', 'CONSTRAINT' => '100', 'comment' => 'Secret', 'default' => '0', 'null' => true),
            'status' => array('type' => 'int', 'comment' => '團隊狀態 0:關閉 1:啟用', 'default' => '0', 'null' => true),
            'CreatedAt' => array('type' => 'int', 'comment' => '', 'default' => '0', 'null' => true),
            'UpdatedAt' => array('type' => 'int', 'comment' => '', 'default' => '0', 'null' => true),
            'DeletedAt' => array('type' => 'int', 'comment' => '', 'default' => '0', 'null' => true),
        ));
        // 設定資料表key值
        $this->dbforge->add_key("id", TRUE);
        // 建立資料表
        $this->dbforge->create_table("team");
        // 建立預設內容 (自動重新建立資料表時可自訂預設內容)
        ####################################################
        ##
        ##
        ##
    }
    public function down() {
        // 移除資料表
        $this->dbforge->drop_table("team");
    }
}