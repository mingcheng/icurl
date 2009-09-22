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
    die(include 'inc/template.inc.html');
}

$request_url  = get_request_var('q', '');
$need_auth    = get_request_var('a', '') ? true : false;
$request_type = get_request_var('r', 'GET');
$agent        = get_request_var('ag', '');
$port         = intval($port    = get_request_var('p', '80')) ? $port : 80;
$timeout      = intval($timeout = get_request_var('t', '2'))  ? $timeout: 2;
$params_name  = get_request_var('n', array());
$params_value = get_request_var('v', array());
$charset      = get_request_var('c', 'utf-8');
$referer      = get_request_var('ref', '');
$http_version = get_request_var('ver', '');


// 验证


$options = array(
    CURLOPT_CUSTOMREQUEST => $request_type,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_PORT => $port,
    CURLOPT_USERAGENT => $agent,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $request_url,
    CURLOPT_REFERER => $referer,
//    CURLOPT_HEADER => true
);

// 是否显示 HTTP 头
$options[CURLOPT_HEADER] = get_request_var('h', '') ? true : false;

// 是否跟随跳转
$options[CURLOPT_FOLLOWLOCATION] = get_request_var('f', '') ? true : false;

if (!empty($params_name) && !empty($params_value)) {
    $options[CURLOPT_POSTFIELDS] = build_params($params_name, $params_value);
}

if ($need_auth) {
    $options[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
    $username = get_request_var('user', '');
    $password = get_request_var('pass', '');
    $options[CURLOPT_USERPWD] = sprintf('%s:%s', $username, $password);
}

switch($http_version) {
    case 'v1.0':
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
        break;
    case 'v1.1':
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        break;
    default:
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_NONE;
}

$handle = curl_init();
curl_setopt_array($handle, $options);
$result = curl_exec($handle);
header('Content-type: text/plain');
//parse_http_response
echo iconv($charset, 'utf-8', $result);
curl_close($handle);
