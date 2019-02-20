<?php
class MainModel extends CI_Model {

    public $db;

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

    /**
     * 新增資料
     *
     * @param [type] $data
     * @param [type] $table
     * @return void
     */
    public function InsertData($data, $table) {
        date_default_timezone_set('Asia/Taipei');
        $data['CreatedAt'] = time();
        $data['UpdatedAt'] = time();
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    /**
     * 修改資料
     *
     * @param [type] $data
     * @param [type] $table
     * @param [type] $key
     * @param [type] $key_value
     * @param string $key1
     * @param string $key_value1
     * @return void
     */
    public function UpdataData($data, $table, $key, $key_value, $key1 = '', $key_value1 = '') {
        $data['UpdatedAt'] = time();
        $this->db->where($key, $key_value);
        if (!empty($key_value1)) {
            $this->db->where($key1, $key_value1);
        }
        $this->db->update($table, $data);
    }

    /**
     * 刪除資料
     *
     * @param string $table
     * @param string $key
     * @param string $keyval
     * @return void
     */
    public function DelData($table = '', $key = '', $keyval = '') {
        if (!empty($table) and !empty($key) and !empty($keyval)) {
            $this->db->from($table);
            $this->db->where($key, $keyval);
            $this->db->delete();
        }
    }

    /**
     * 假刪資料
     *
     * @param string $table
     * @param string $key
     * @param string $key_value
     * @param array $QuerySet
     * @return void
     */
    public function DelItem($table = '', $key = '', $key_value = '', $QuerySet = array()) {

        $data = array(
            'DeletedAt' => time(),
        );
        if (!empty($QuerySet)) {
            foreach ($QuerySet as $queryIndex => $content) {
                $this->db->where($queryIndex, $content);
            }
        }
        if (!empty($key_value)) {
            $this->db->where($key, $key_value);
        }
        $this->db->update($table, $data);

        // echo $this->db->last_query() . "<br>";
        // log_message('error', '### 移除 SQL: ' . $this->db->last_query());
    }

    /**
     * 取得 cache 資料
     *
     * @param string $name
     * @return void
     */
    public function getMEMData($name = '') {
        $this->load->driver('cache');
        return $this->cache->memcached->get($name);
    }

    /**
     * 新增 cache 資料
     *
     * @param string $name
     * @param array $data
     * @param integer $times
     * @return void
     */
    public function newMEMData($name = '', $data = [], $times = 300) {
        $this->load->driver('cache');
        $this->cache->memcached->save($name, $data, $times);
    }

    /**
     * 刪除 cache 資料
     *
     * @param string $name
     * @return void
     */
    public function deleteMEMData($name = '') {
        $this->load->driver('cache');
        $this->cache->memcached->delete($name);
    }
}