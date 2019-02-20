<?php
defined('BASEPATH') OR exit('No direct script access allowed');
###################################################################
#
#
#                       _oo0oo_
#                      o8888888o
#                      88" . "88
#                      (| -_- |)
#                      0\  =  /0
#                    ___/`---'\___
#                  .' \\|     |// '.
#                 / \\|||  :  |||// \
#                / _||||| -:- |||||- \
#               |   | \\\  -  /// |   |
#               | \_|  ''\---/''  |_/ |
#               \  .-\__  '-'  ___/-. /
#             ___'. .'  /--.--\  `. .'___
#          ."" '<  `.___\_<|>_/___.' >' "".
#         | | :  `- \`.;`\ _ /`;.`/ - ` : | |
#         \  \ `_.   \_ __\ /__ _/   .-` /  /
#     =====`-.____`.___ \_____/___.-`___.-'=====
#                       `=---='
#
#
#     ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
#
#               佛祖保佑         永無BUG
#
###################################################################
class Socket extends CI_Controller {
    public $site;
    public function __construct() {
        parent::__construct();
        // 載入必Load項目
        $this->load->helper("Common_Helper");
        $this->load->model('MainModel');
        // 設置時區
        date_default_timezone_set('Asia/Taipei');
    }

    // 重新設置線上清單
    public function reset() {
        $this->MainModel->deleteMEMData('AddressBook');
        echo 'done.' . PHP_EOL;
    }

    // 啟動 Socket Server
    public function Server() {

        echo "### " . date('Y-m-d H:i:s') . " Start Web Socket Server." . PHP_EOL;

        // 基礎設定參數
        $setConfig = [
            'worker_num' => 4,
            'ssl_key_file' => APPPATH . '../../ssl/' . ENVIRONMENT . '/privkey1.pem',
            'ssl_cert_file' => APPPATH . '../../ssl/' . ENVIRONMENT . '/cert1.pem',
            'ssl_cafile' => APPPATH . '../../ssl/' . ENVIRONMENT . '/fullchain1.pem',
        ];

        // 建立 Server 物件

        if (ENVIRONMENT == 'development') {
            // 設定基礎參數
            $server = new swoole_websocket_server("0.0.0.0", 12019, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        } else {
            // 設定基礎參數
            $server = new swoole_websocket_server("0.0.0.0", 12019, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
            $server->set($setConfig);
        }

        // 初始化連線
        $server->on('Request', function ($request, $response) {
            echo '*** start Request ***' . PHP_EOL;
            $response->header('Access-Control-Allow-Origin', $request->header['origin'] ?? '');
            $response->header('Access-Control-Allow-Methods', 'OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'x-requested-with,session_id,Content-Type,token,Origin');
            $response->header('Access-Control-Max-Age', '86400');
            $response->header('Access-Control-Allow-Credentials', 'true');

            if ($request->server['request_method'] == 'OPTIONS') {
                $response->status(200);
                $response->end();
                return;
            };

            $_GET = $request->get;
            $_POST = $request->post;
            $_COOKIE = $request->cookie;
            $_FILES = $request->files;

            echo '_GET: ' . json_encode($_GET) . PHP_EOL;
            echo '_POST: ' . json_encode($_POST) . PHP_EOL;
            echo '_COOKIE: ' . json_encode($_COOKIE) . PHP_EOL;
            echo '_FILES: ' . json_encode($_FILES) . PHP_EOL;

            // websocket握手连接算法验证
            $secWebSocketKey = $request->header['sec-websocket-key'];
            $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
            if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
                $response->end();
                return false;
            }
            echo $request->header['sec-websocket-key'];
            $key = base64_encode(sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            ));

            $headers = [
                'Upgrade' => 'websocket',
                'Connection' => 'Upgrade',
                'Sec-WebSocket-Accept' => $key,
                'Sec-WebSocket-Version' => '13',
            ];

            if (isset($request->header['sec-websocket-protocol'])) {
                $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
            }

            foreach ($headers as $key => $val) {
                $response->header($key, $val);
            }

            $request->server->defer();

            $response->status(101);
            $response->end();
        });

        // 連線開啟
        $server->on('open', function ($server, $req) {
            echo '*** start open ***' . PHP_EOL;
            // ===============
            // $_GET = $req->get;
            // echo '_GET: ' . json_encode($_GET) . PHP_EOL . PHP_EOL;
        });

        // 連線訊息觸發
        $server->on('message', function ($server, $frame) {
            echo '*** start message ' . date('Y-m-d H:i:s') . ' ***' . PHP_EOL;
            // echo "object info: " . json_encode($frame) . PHP_EOL;
            echo "=> server connection id: " . $frame->fd . PHP_EOL;
            echo "=> server received message: " . $frame->data . PHP_EOL;

            // 接收傳入資料
            $postData = json_decode($frame->data, true);

            // 用戶端應用程序
            if ($postData['platform'] == 'client') {
                $results = $this->client_message($server, $frame);
            }

            // 服務端應用程序
            if ($postData['platform'] == 'service') {
                $results = $this->service_message($server, $frame);
            }

            // 回覆內容
            echo "=> Need Reply Messages: " . json_encode($results) . PHP_EOL;
            foreach ($results as $rowIndex => $row) {
                if (!empty($row['channel_ids']) && !empty($row['data'])) {
                    foreach ($row['channel_ids'] as $channel) {
                        $server->push($channel, json_encode($row['data']));
                        echo '=> server send to [' . $channel . '] message: ' . json_encode($row['data']) . PHP_EOL;
                    }
                }
            }
            echo PHP_EOL;
        });

        $server->on('receive', function (swoole_server $serv, $fd, $reactor_id, $data) {
            echo '*** start receive ' . date('Y-m-d H:i:s') . ' ***' . PHP_EOL;
            echo "[#" . $serv->worker_id . "]\tClient[$fd]: $data\n";
            echo PHP_EOL . PHP_EOL;
        });

        // 連線關閉事件
        $server->on('close', function ($server, $fd) {
            echo '*** start close ' . date('Y-m-d H:i:s') . ' ***' . PHP_EOL;
            echo 'fd: ' . json_encode($fd) . PHP_EOL;
            // 移除專屬位址對應資料並保持 48 小時
            $getAddressBook = $this->MainModel->getMEMData('AddressBook');
            unset($getAddressBook[$fd]);
            $this->MainModel->newMEMData('AddressBook', $getAddressBook, (60 * 60 * 48));
            echo "=> last AddressBook: " . json_encode($getAddressBook) . PHP_EOL . PHP_EOL;
        });

        // 啟動 Server
        $server->start();
        echo "=> start websocket server on: 0.0.0.0:12019" . PHP_EOL;
    }

    // 用戶端應用程序
    public function client_message($server, $frame) {

        // 接收傳入資料
        $postData = json_decode($frame->data, true);

        // 執行用戶端 Token 驗證
        $result = [];
        if (!empty($postData['OAUTH-API-KEY'])) {
            // 設定專屬位址
            $conn_key = "socket-conn-" . $postData['OAUTH-API-KEY'];
            echo '=> conn_key: ' . $conn_key . PHP_EOL;
            // 取得已專屬位址資料
            $getCacheToken = $this->MainModel->getMEMData($conn_key);
            // 設定專屬位址對應資料並保持 48 小時
            $getAddressBook = $this->MainModel->getMEMData('AddressBook');
            $AddressBook = !empty($getAddressBook) ? $getAddressBook : [];
            $AddressBook[$frame->fd] = ['OAUTH-API-KEY' => $postData['OAUTH-API-KEY'], 'last_time' => time()];
            $this->MainModel->newMEMData('AddressBook', $AddressBook, (60 * 60 * 48));
            echo "=> last AddressBook: " . json_encode($AddressBook) . PHP_EOL . PHP_EOL;
            // 寫入連線設置
            $this->MainModel->newMEMData($conn_key, ['channel' => $frame->fd, 'API-KEY' => $postData['OAUTH-API-KEY'], 'last_time' => time()], (60 * 60 * 1));
            echo '=> connection 已儲存' . PHP_EOL;

            switch ($postData['type']) {
            case 'start':
                $result = [];
                break;
            case 'chat':
                $result = $this->chat_message($server, $frame);
                break;
            default:
                $result[] = [
                    'channel_ids' => [$frame->fd],
                    'data' => [
                        'error_code' => 400, 'type' => 'message', 'message' => '操作異常',
                    ],
                ];
                break;
            }
        } else {
            $result[] = [
                'channel_ids' => [$frame->fd],
                'data' => [
                    'error_code' => 400, 'type' => 'message', 'message' => 'You have No API Token.',
                ],
            ];
        }
        return $result;
    }

    // 服務端應用程序
    public function service_message($server, $frame) {
        $getAddressBook = $this->MainModel->getMEMData('AddressBook');

        // 接收傳入資料
        $postData = json_decode($frame->data, true);

        $result = [];
        if (!empty($postData['type'])) {
            switch ($postData['type']) {
            // 測試應用
            case 'test':
                $result[0] = ['channel_ids' => []];
                foreach ($getAddressBook as $channel => $context) {
                    $result[0]['channel_ids'][] = $channel;
                }
                $result[0]['data'] = ['error_code' => 0, 'type' => 'message', 'message' => 'service is work.'];
                break;
            // 發布通知
            case 'message':
                if (!empty($postData['to'])) {
                    $result[0] = ['channel_ids' => []];
                    foreach ($postData['to'] as $to) {
                        // 設定專屬位置
                        $conn_key = "socket-conn-" . $to;
                        // 取得已專屬位址資料
                        $getCacheToken = $this->MainModel->getMEMData($conn_key);
                        // 製作訊息內容
                        if (!empty($getCacheToken)) {
                            echo "=> 指定 Token [" . $to . "]使用的聊天頻道是: " . $getCacheToken['channel'] . PHP_EOL;
                            echo "=> 指定聊天頻道[" . $getCacheToken['channel'] . "]的使用者目前為: " . $getAddressBook[$getCacheToken['channel']]['OAUTH-API-KEY'] . PHP_EOL;
                            if (!empty($getAddressBook[$getCacheToken['channel']]) && $getAddressBook[$getCacheToken['channel']]['OAUTH-API-KEY'] == $to) {
                                echo "=> 指定通知會員[" . $to . "]正在線上" . PHP_EOL;
                                $result[0]['channel_ids'][] = $getCacheToken['channel'];
                                $result[0]['data'] = $postData['data'];
                            } else {
                                echo "=> 指定通知會員[" . $to . "]不在線上" . PHP_EOL;
                            }
                        } else {
                            echo "=> 指定通知會員[" . $to . "]不在線上" . PHP_EOL;
                        }
                    }
                    if (empty($result[0]['channel_ids'])) {
                        $result[0] = false;
                    }
                }
                break;
            // 學員過卡
            case 'crad_pass':
                echo "=> 觸發學員過卡程序..." . PHP_EOL;
                if (!empty($postData['to'])) {
                    $result[0] = ['channel_ids' => []];
                    foreach ($postData['to'] as $to) {
                        // 設定專屬位置
                        $conn_key = "socket-conn-" . $to;
                        // 取得已專屬位址資料
                        $getCacheToken = $this->MainModel->getMEMData($conn_key);
                        // 製作訊息內容
                        if (!empty($getCacheToken)) {
                            echo "=> 指定 Token [" . $to . "]使用的聊天頻道是: " . $getCacheToken['channel'] . PHP_EOL;
                            echo "=> 指定聊天頻道[" . $getCacheToken['channel'] . "]的使用者目前為: " . $getAddressBook[$getCacheToken['channel']]['OAUTH-API-KEY'] . PHP_EOL;
                            if (!empty($getAddressBook[$getCacheToken['channel']]) && $getAddressBook[$getCacheToken['channel']]['OAUTH-API-KEY'] == $to) {
                                echo "=> 指定通知會員[" . $to . "]正在線上" . PHP_EOL;
                                $result[0]['channel_ids'][] = $getCacheToken['channel'];
                                $result[0]['data'] = $postData['data'];
                            } else {
                                echo "=> 指定通知會員[" . $to . "]不在線上" . PHP_EOL;
                            }
                        } else {
                            echo "=> 指定通知會員[" . $to . "]不在線上" . PHP_EOL;
                        }
                    }
                    if (empty($result[0]['channel_ids'])) {
                        $result[0] = false;
                    }
                }
                break;
            default:
                $result[0] = [];
                break;
            }
        } else {
            $result[0] = [];
        }
        return $result;
    }

    // 即時通訊程序
    public function chat_message($server, $frame) {

        // 接收傳入資料
        $postData = json_decode($frame->data, true);

        // 取得收信人資料
        $error = "";
        $error = (empty($postData['channel']['to'])) ? "沒有指定收件人" : $error;
        $error = (empty($postData['channel']['platform'])) ? "沒有指定收件平台" : $error;

        if (empty($error)) {
            // 產生發信人的訊息內容
            $result[] = [
                'channel_ids' => [$frame->fd],
                'data' => [
                    'error_code' => 0,
                    'class' => 'sender',
                    'type' => 'chat',
                    'message' => $postData['message'],
                ],
            ];
            // 產生收信人的訊息內容
            $query_set = [
                'select' => 'login_token', 'login_userId' => $postData['channel']['to'], 'login_platform' => $postData['channel']['platform'],
            ];
            $this->load->model('MessageModel');
            $getTokensData = $this->MessageModel->getMemberToken($query_set);
            $tokens = (!empty($getTokensData)) ? $getTokensData['data'] : [];
            // 篩選出收件人所在的頻道
            $rev_ids = [];
            foreach ($tokens as $token) {
                $to = $token['login_token'];
                // 設定專屬位置
                $conn_key = "socket-conn-" . $to;
                // 取得已專屬位址資料
                $getCacheToken = $this->MainModel->getMEMData($conn_key);
                // 製作訊息內容
                if (!empty($getCacheToken)) {
                    echo "=> 指定 Token [" . $to . "]使用的聊天頻道是: " . $getCacheToken['channel'] . PHP_EOL;
                    echo "=> 指定聊天頻道[" . $getCacheToken['channel'] . "]的使用者目前為: " . $getAddressBook[$getCacheToken['channel']]['OAUTH-API-KEY'] . PHP_EOL;
                    if (!empty($getAddressBook[$getCacheToken['channel']]) && $getAddressBook[$getCacheToken['channel']]['OAUTH-API-KEY'] == $to) {
                        echo "=> 指定通知會員[" . $to . "]正在線上" . PHP_EOL;
                        $rev_ids[] = $getCacheToken['channel'];
                    } else {
                        echo "=> 指定通知會員[" . $to . "]不在線上" . PHP_EOL;
                    }
                } else {
                    echo "=> 指定通知會員[" . $to . "]不在線上" . PHP_EOL;
                }
            }
            // 產生訊息內容
            if (!empty($rev_ids)) {
                $result[] = [
                    'channel_ids' => $rev_ids,
                    'data' => [
                        'error_code' => 0,
                        'class' => 'receiver',
                        'type' => 'chat',
                        'message' => $postData['message'],
                    ],
                ];
            } else {
                $result[] = [
                    'channel_ids' => [$frame->fd],
                    'data' => [
                        'error_code' => 400, 'type' => 'message', 'title' => '對方不在線上', 'message' => '對方已經離線，暫時無法發送訊息哦！',
                    ],
                ];
            }
        } else {
            log_message('error', '=> Send Message Function Is Error: ' . $error);
            log_message('error', '=> Send Data: ' . json_encode($postData));
            $result[] = [
                'channel_ids' => [$frame->fd],
                'data' => [
                    'error_code' => 400, 'type' => 'message', 'title' => '送出失敗', 'message' => '發送訊息時發生錯誤，無法送出訊息！',
                ],
            ];
        }

        return $result;
    }
}