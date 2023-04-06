<?php

/*

سورس ربات و وبسرویس اسکیرین شات از سایت ها و صفحالت وب
کانال ما : @DimoTM
نویسنده : @DevUltra
منبع بزن.

*/

header('Content-Type: Application/Json');
error_reporting(E_ALL ^ E_NOTICE);

$_ = $_GET['url'];
$__ = $_GET['full'];

function shot($url){
    
    global $__;
    
    $fields = array(
        'key' => 'qudlmGidDdFAjSlghG4GYgFH5QejRoqL8SeMyAwbeEycDYBiz1',
        'url' => $url,
        'height' => 1024,
        'width' => 1024
    );
    
    if($__ == 'true'){
        
        $fields = array(
            'key' => 'qudlmGidDdFAjSlghG4GYgFH5QejRoqL8SeMyAwbeEycDYBiz1',
            'url' => $url
        );
        
    }
    
    $fields = json_encode($fields);
    $ch = curl_init('http://screeenly.com/api/v1/fullsize');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 35);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($fields)));
    $res = json_decode(curl_exec($ch));
    return $res->path;
    
}

if(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $_)) die(json_encode(['ok' => true, 'writer' => 'T.me/DevUtra', 'channel' => 'T.me/DimoTM', 'description' => 'Your url in invalid!'], 448));

$res = shot($_);

if(is_null($res)) die(json_encode(['ok' => true, 'writer' => 'T.me/DevUtra', 'channel' => 'T.me/DimoTM', 'description' => 'Error in the processes!'], 448));

$end = [
    'ok' => true,
    'writer' => 'T.me/DevUtra',
    'channel' => 'T.me/DimoTM',
    'url' => $_,
    'screenshot' => $res
];

echo json_encode($end, 448);

/*

سورس ربات و وبسرویس اسکیرین شات از سایت ها و صفحالت وب
کانال ما : @DimoTM
نویسنده : @DevUltra
منبع بزن.

*/

?>