<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * iCurl
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-09-17
 * @link   http://www.gracecode.com/
 */

require_once 'inc/func.inc.php';

// 读取配置文件
$_CONFIG = parse_ini_file('config.ini');

if (empty($_POST)) {
    die(include 'template.inc.html');
}

$request_url  = get_request_var('q', '');
$request_type = get_request_var('r', 'GET');
$agent        = get_request_var('ag', '');
$port         = intval($port    = get_request_var('p', '80')) ? $port : 80;
$timeout      = intval($timeout = get_request_var('t', '2'))  ? $timeout: 2;
$params_name  = get_request_var('n', array());
$params_value = get_request_var('v', array());

// 验证


$options = array(
    CURLOPT_CUSTOMREQUEST => $request_type,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_PORT => $port,
    CURLOPT_USERAGENT => $agent,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $request_url,
//    CURLOPT_HEADER => true
//    CURLOPT_REFERER
);

// 是否显示 HTTP 头
$options[CURLOPT_HEADER] = get_request_var('h', '') ? true : false;

// 是否跟随跳转
$options[CURLOPT_FOLLOWLOCATION] = get_request_var('f', '') ? true : false;

if (!empty($params_name) && !empty($params_value)) {
    $options[CURLOPT_POSTFIELDS] = build_params($params_name, $params_value);
}

//$options[CURLOPT_BINARYTRANSFER] = true;

//var_dump($options);

$handle = curl_init();
curl_setopt_array($handle, $options);
$result = curl_exec($handle);
header('Content-type: text/plain');
//parse_http_response
echo $result;
curl_close($handle);
