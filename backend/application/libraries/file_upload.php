<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_upload extends CI_Controller {

    protected $CI;

    // 初始化
    function __construct() {
        parent::__construct();
        // =========================
        $this->CI = &get_instance();
        // =========================
        $this->CI->load->helper("Common_Helper");
    }

    // 處理檔案上傳
    public function files($_fileData = [], $_path = "/storage/temp") {

        // 設定基礎位置
        $files_base_path = APPPATH . '../../public/storage';
        // 產生 Temp 檔案名稱
        $newFileName = GenerAtorMoreEng(8);
        // 組合目標目錄
        $upload_dir = $files_base_path . $_path;
        // 組合前端可呼叫位置
        $fileOnlineUrlPath = $_path . '/';

        if (!empty($_fileData['fileToUpload'])) {
            $_fileData['files'] = $_fileData['fileToUpload'];
        }

        if (isset($_fileData['files'])) {

            log_message('error', 'file: ' . json_encode($_fileData['files']));

            $uploadFile = [];
            if (is_array($_fileData['files']['name'])) {
                // 多檔案模式
                foreach ($_fileData['files']['name'] as $fileIndex => $fileName) {
                    $uploadFile[$fileIndex] = [
                        'name' => $_fileData['files']['name'][$fileIndex],
                        'type' => $_fileData['files']['type'][$fileIndex],
                        'tmp_name' => $_fileData['files']['tmp_name'][$fileIndex],
                        'error' => $_fileData['files']['error'][$fileIndex],
                        'size' => $_fileData['files']['size'][$fileIndex],
                    ];
                }
            } else {
                // 單檔案模式
                $uploadFile[0] = $_fileData['files'];
            }

            if (!file_exists($upload_dir)) {
                //不存在的話就創建upload資料夾
                if (!mkdir($upload_dir, 0775, true)) {
                    $result = array(
                        'code' => 108,
                        'message' => '上傳目錄不存在，並且創建失敗！',
                        'path_dir' => $upload_dir,
                    );
                }
                chmod($upload_dir, 0775);
            }

            if (file_exists($upload_dir)) {
                $result = [
                    'code' => 0,
                    'message' => '',
                    'files' => [],
                ];
                foreach ($uploadFile as $rowIndex => $fileRow) {

                    $code = 0;
                    $err_message = '';
                    //$fileElementName = $updateobj;
                    $fileToUpload = '';
                    $movefile = '';

                    //如果錯誤代碼為 UPLOAD_ERR_OK，表非上傳成功
                    $fileErrorKey = false;
                    if (!empty($fileRow['error'])) {
                        switch ($fileRow['error']) {
                        case '1':
                            $err_message = '上傳文件大小超過服務器容許最大值！';
                            break;
                        case '2':
                            $err_message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                            break;
                        case '3':
                            $err_message = 'The uploaded file was only partially uploaded';
                            break;
                        case '4':
                            $err_message = '沒有檔案被上傳..';
                            break;
                        case '6':
                            $err_message = 'Missing a temporary folder';
                            break;
                        case '7':
                            $err_message = 'Failed to write file to disk';
                            break;
                        case '8':
                            $err_message = 'File upload stopped by extension';
                            break;
                        case '999':
                        default:
                            $err_message = 'No error code avaiable';
                        }
                    } elseif (empty($fileRow['tmp_name']) || $fileRow['tmp_name'] == 'none') {
                        $err_message = '您似乎並沒有上傳檔案喔！';
                    } else {
                        //亂數產生檔案名稱
                        $str = '';
                        for ($i = 1; $i <= 12; $i++) {
                            $n = rand(1, 2);
                            switch ($n) {
                            case 1;
                                $str = $str . chr(rand(65, 90)); #大寫英文
                                break;
                            case 2;
                                $str = $str . chr(rand(97, 122)); #小寫英文
                                break;
                            case 3;
                                $str = $str . chr(rand(48, 57)); #數字
                                break;
                            }
                        }

                        $file_name = explode('.', strtolower($fileRow['name']));
                        $file_type = $file_name[(count($file_name) - 1)];
                        $movefile = $upload_dir . '/' . $newFileName . '-' . $str . '.' . $file_type;

                        if ($file_type == 'jpg' or $file_type == 'png' or $file_type == 'jpeg') {

                            //處理圖片上傳
                            $fileErrorKey = true;
                            if ($fileRow['error'] == UPLOAD_ERR_OK) {
                                //隨機產生新的檔案名稱
                                //$upfile_name = GenerAtorMoreEng(5).'-'.GenerAtorMoreEng(7);
                                //將暫存檔搬移到上傳目錄下，並且改回原始檔名
                                move_uploaded_file($fileRow['tmp_name'], $movefile);
                                //顯示上傳檔案的相關訊息
                                //echo '上傳成功....';
                                $fileToUpload = $newFileName . '-' . $str . '.' . $file_type;
                            } else {
                                //echo"上傳失敗";
                                $code = 101;
                                $fileToUpload = '';
                            }

                        } else if ($file_type == 'xlsx') {

                            //處理檔案上傳
                            $filekey = true;
                            if ($fileRow['error'] == UPLOAD_ERR_OK) {
                                move_uploaded_file($fileRow['tmp_name'], $movefile);
                                $fileToUpload = $newFileName . '-' . $str . '.' . $file_type;
                            } else {
                                //echo"上傳失敗";
                                $code = 102;
                                $fileToUpload = '';
                            }
                        } else if ($file_type == 'pdf' || $file_type == 'zip') {

                            //處理檔案上傳
                            $filekey = true;
                            if ($fileRow['error'] == UPLOAD_ERR_OK) {
                                move_uploaded_file($fileRow['tmp_name'], $movefile);
                                $fileToUpload = $newFileName . '-' . $str . '.' . $file_type;
                            } else {
                                //echo"上傳失敗";
                                $code = 103;
                                $fileToUpload = '';
                            }

                        } else if ($file_type == 'mp3') {

                            //處理檔案上傳
                            $filekey = true;
                            if ($fileRow['error'] == UPLOAD_ERR_OK) {
                                move_uploaded_file($fileRow['tmp_name'], $movefile);
                                $fileToUpload = $newFileName . '-' . $str . '.' . $file_type;
                            } else {
                                //echo"上傳失敗";
                                $code = 104;
                                $fileToUpload = '';
                            }

                        } else {
                            $code = 105;
                            $err_message = '檔案格式不符可接受標準';
                        }

                        // $err_message .= " File Name: " . $fileToUpload . ", ";
                        // $err_message .= " File Size: " . @filesize($fileRow['tmp_name']);

                    }

                    if ($pagemod == '/ckimg') {
                        $CKEditorFuncNum = isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditorFuncNum'] : 1;
                        echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction( ' . $CKEditorFuncNum . ' ,"' . $fileOnlineUrlPath . $fileToUpload . '", "上傳成功");</script>';
                        exit();
                    } else {
                        $result['code'] = ($code > 0) ? $code : 0;
                        $result['message'] = (!empty($err_message)) ? $err_message : $result['message'];
                        $result['files'][$rowIndex] = [
                            'code' => $code,
                            'message' => $err_message,
                            'file_name' => $fileRow['name'],
                            'file_url' => $fileOnlineUrlPath . $fileToUpload,
                        ];
                    }
                }
            } else {
                $result = array(
                    'code' => 106,
                    'message' => '上傳檔案時因目錄發生錯誤，請重新嘗試',
                );
            }
        } else {
            $result = array(
                'code' => 107,
                'message' => '上傳檔案中途失敗,請重新嘗試上傳',
                'file_name' => (!empty($_fileData['files'])) ? $_fileData['files'] : "Null",
            );
        }

        return $result;
    }
}