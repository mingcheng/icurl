<?php
define('ICURL_DATABASE', 'data/sqlite.db');

$db = new PDO('sqlite:'.ICURL_DATABASE);

$db->exec('DROP TABLE icurl');
$db->exec('CREATE TABLE icurl (id integer primary key, data BLOB not NULL UNIQUE, flag varchar(255) not NULL UNIQUE, _date NUMERIC)');
var_dump($db->errorInfo());

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


$data = 'a:10:{i:10036;s:3:"GET";i:13;s:1:"5";i:3;s:2:"80";i:10018;s:93:"Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 GTB5";i:19913;b:1;i:10002;s:23:"http';

var_dump(write_params($data, $db));
var_dump($db->errorInfo());
$result = read_params(md5($data), $db);
var_dump($db->errorInfo());
var_dump(($result));
