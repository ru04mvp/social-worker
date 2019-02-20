<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-Type: application/json');
print_r(json_encode(['code' => 404, 'heading' => $heading, 'message' => strip_tags($message)]));