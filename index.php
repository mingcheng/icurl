<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * iCurl
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-09-17
 * @link   http://www.gracecode.com/
 */

require_once 'Curl.inc.php';

$_CONF = parse_ini_file('config.ini');

$request_url = 'http://127.0.0.1/icurl/request.php?a=b&b=c';
$handle = curl_init();
curl_setopt_array($handle, array(
    CURLOPT_HTTPGET => true,
    CURLOPT_HEADER  => true,
    //CURLOPT_TIMEOUT => 5,
    //CURLOPT_PORT => 80,
    CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'a=b&b=c',
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_URL => $request_url
));
$result = curl_exec($handle);

header('Content-type: text/plain');
echo $result;
