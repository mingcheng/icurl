<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * iCurl
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-09-17
 * @link   http://www.gracecode.com/
 * @change
 *     [+]new feature  [*]improvement  [!]change  [x]bug fix
 *
 * [+] 2009-09-28
 *      优化界面，完善脚本
 *
 * [+] 2009-09-21
 *      初始化版本，完成基本功能
 */

define('ICURL_VERSION', '$Id$');
define('ICURL_DATABASE', 'data/sqlite.db');

require_once 'inc/func.inc.php';

// 检查扩展是否满足要求
$extensions = get_loaded_extensions();
if (!in_array('curl', $extensions) || !in_array('filter', $extensions)
    || !in_array('iconv', $extensions) || !in_array('pdo_sqlite', $extensions)) {
    die('Missing Extensions, Pls recheck ur PHP environment.');
}

$Database = new PDO('sqlite:'.ICURL_DATABASE);

/*
$Database->exec('DROP TABLE icurl');
$Database->exec('CREATE TABLE icurl (id integer primary key, data BLOB not NULL UNIQUE, flag varchar(255) not NULL UNIQUE, _date NUMERIC)');
var_dump($Database->errorInfo());
exit;
 */

if (!empty($_POST)) {
    // 根据 POST 信息获取参数
    $request_url  = get_request_var('q', '');
    $need_auth    = get_request_var('a', '') ? true : false;
    $binary       = get_request_var('b', '');
    $request_type = get_request_var('r', 'GET');
    $agent        = get_request_var('ag', '');
    $port         = intval($port    = get_request_var('p', '80')) ? $port : 80;
    $timeout      = intval($timeout = get_request_var('t', '2'))  ? $timeout: 2;
    $params_name  = get_request_var('n', array());
    $params_value = get_request_var('v', array());
    $charset      = get_request_var('c', 'utf-8');
    $referer      = get_request_var('ref', '');
    $http_version = get_request_var('ver', '');
    $save         = get_request_var('save', '');

    if(filter_var($request_url, FILTER_VALIDATE_URL) === false) {
        echo_result("Sorry, $request_url not valid!");
        exit;
    }

    $options = array(
        CURLOPT_CUSTOMREQUEST => $request_type,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_PORT => $port,
        CURLOPT_USERAGENT => $agent,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $request_url,
        CURLOPT_REFERER => $referer,
        // CURLOPT_HEADER => true
    );

    // 是否显示 HTTP 头
    $options[CURLOPT_HEADER] = get_request_var('h', '') ? true : false;

    // 是否跟随跳转
    $options[CURLOPT_FOLLOWLOCATION] = get_request_var('f', '') ? true : false;

    if (!empty($params_name) && !empty($params_value)) {
        $params = build_params($params_name, $params_value);
        if (strlen(params)) {
            if ($request_type == 'POST') {
                $options[CURLOPT_POSTFIELDS] = $params;
            } elseif ($request_type == 'GET') {
                if (strstr($request_url, '?')) {
                    $options[CURLOPT_URL] = $request_url . '&' . $params;
                } else {
                    $options[CURLOPT_URL] = $request_url . '?' . $params;
                }
            } else {
                // ...
            }
        }
    }

    if ($need_auth) {
        $options[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
        $username = get_request_var('user', '');
        $password = get_request_var('pass', '');
        $options[CURLOPT_USERPWD] = sprintf('%s:%s', $username, $password);
    /*
    $options[CURLOPT_SSL_VERIFYHOST] = 1;
    $options[CURLOPT_SSL_VERIFYPEER] = false;
     */
        $options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
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

    if ($binary) {
        $options[CURLOPT_HEADER] = false;
    }
} else {
    // 根据 URL 参数读取参数
    // df021be6750f1a463bdca54d07bf39e9
    preg_match('/\/icurl\/(\w+)\//i', $_SERVER["REQUEST_URI"], $match);
    if (isset($match[1]) && $serialized = read_params($match[1], $Database)) {
        $load_from_database = true;
        $options = unserialize($serialized['data']);
        // 便于使用，不输出 HTTP 头
        $options[CURLOPT_HEADER] = false;
    }
}

// 仍然无，则显示程式界面
if (empty($options)) {
    echo_template();
    exit;
}

// 如果需要保存
$serialized = serialize($options);
if ($save) {
    @write_params($serialized, $Database);
}
$params_serialized = md5($serialized);

// just do it!
$handle = curl_init();
curl_setopt_array($handle, $options);
$result = curl_exec($handle);

// output
if ($load_from_database) {
    echo $result;
} else if ($binary) {
    header('Content-Disposition:attachment; filename="'.basename($options[CURLOPT_URL]).'"');
    echo $result;
} else {
    @echo_result(iconv($charset, 'utf-8', $result), $params_serialized);
}

curl_close($handle);
$Database = null;
