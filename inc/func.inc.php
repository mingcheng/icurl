<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:


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

function build_params($name, $value) {
    for ($result = array(), $i = 0, $len = sizeof($name); $i < $len; $i++) {
        if (strlen($name[$i])) {
            $result[] = sprintf('%s=%s', $name[$i], urlencode($value[$i]));
        }
    }

    return implode('&', $result);
}

function parse_http_response ($string) {
    $headers = array(); $content = ''; $str = strtok($string, "\n"); $h = null;
    while ($str !== false) {
        if ($h and trim($str) === '') {
            $h = false;
            continue;
        }
        if ($h !== false and false !== strpos($str, ':')) {
            $h = true;
            list($headername, $headervalue) = explode(':', trim($str), 2);
            $headername = strtolower($headername);
            $headervalue = ltrim($headervalue);
            if (isset($headers[$headername]))
                $headers[$headername] .= ',' . $headervalue;
            else
                $headers[$headername] = $headervalue;
        }
        if ($h === false) {
            $content .= $str."\n";
        }
        $str = strtok("\n");
    }
    return array($headers, trim($content));
}
