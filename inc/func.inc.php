<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:

if (!function_exists('curl_setopt_array')) {
   function curl_setopt_array(&$ch, $curl_options)
   {
       foreach ($curl_options as $option => $value) {
           if (!curl_setopt($ch, $option, $value)) {
               return false;
           } 
       }
       return true;
   }
}

/**
 * 获取外部请求数据，如 POST、GET
 *
 * @param  string $request_name
 * @param  mixed  $default_value
 * @param  string $method
 * @return mixed
 */
function get_request_var($request_name, $default_value = null, $method = "all") {
    $magic_quotes = ini_get("magic_quotes_gpc") ? true : false;
    $method = strtolower($method);

    switch (strtolower($method)) {
    default:
        case "all":
            if (isset($_POST[$request_name])) {
                return $magic_quotes ? stripslashes($_POST[$request_name]) : $_POST[$request_name];
            } else if (isset($_GET[$request_name])) {
                return $magic_quotes ? stripslashes($_GET[$request_name]) : $_GET[$request_name];
            } else if (isset($_REQUEST[$request_name])) {
                return $magic_quotes ? stripslashes($_REQUEST[$request_name]) : $_REQUEST[$request_name];
            } else {
                return $default_value;
            }
        case "get":
            if (isset($_GET[$request_name])) {
                return $magic_quotes ? stripslashes($_GET[$request_name]) : $_GET[$request_name];
            } else {
                return $default_value;
            }
        case "post":
            if (isset($_POST[$request_name])) {
                return $magic_quotes ? stripslashes($_POST[$request_name]) : $_POST[$request_name];
            } else {
                return $default_value;
            }
        default:
            return $default_value;
    }
}


// 根据参数拼接 URL 字符串
function build_params($name, $value) {
    for ($result = array(), $i = 0, $len = sizeof($name); $i < $len; $i++) {
        if (strlen($name[$i])) {
            $result[] = sprintf('%s=%s', $name[$i], urlencode($value[$i]));
        }
    }

    return implode('&', $result);
}

// 返回结果页
function echo_result($result, $params_serialized) {
    @include 'inc/iframe.inc.html';
}

// 显示主页面
function echo_template() {
    die(@include 'inc/template.inc.html');
}

// Sqlite 数据库存储
function write_params($data, &$db) {
    $stmt = $db->prepare("INSERT INTO icurl (data, flag, _date) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $data);
    $stmt->bindParam(2, md5($data));
    $stmt->bindParam(3, time());
    $stmt->execute();
    return $db->lastInsertId() ? true : false;
}


function read_params($flag, &$db) {
    $stmt = $db->prepare("SELECT id, data, flag FROM icurl WHERE flag = ? LIMIT 1");
    $stmt->execute(array($flag));
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
