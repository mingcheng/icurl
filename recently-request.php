<?php
//header('Content-type: text/plain');
define('ICURL_DATABASE', 'data/sqlite.db');

$db = new PDO('sqlite:'.ICURL_DATABASE);

$stmt = $db->prepare("SELECT id, data, flag, _date FROM icurl ORDER BY _date DESC");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (sizeof($result)) {
    foreach ($result as $item) {
        $options = unserialize($item['data']);
        if ($curlopt_url = $options[CURLOPT_URL]) {
            printf('%04d | %s | <a title="%s" target="_blank" href="%s">%s</a><br />', 
                $item['id'], $item['flag'], date('r', $item['_date']), $curlopt_url, $curlopt_url);
        }
    }
}

$db = null;
