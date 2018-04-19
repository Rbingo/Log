<?php
/**
 * Created by PhpStorm.
 * User: Bingo
 * Date: 2017/12/27
 * Time: 20:20
 */
function send($host,$port , $message = '') {
    $url = 'http://' . $host . ':'.$port;
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($message) ? http_build_query($message) : $message);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $headers = array(
        "Content-Type: application/json;charset=UTF-8"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置header
    $txt = curl_exec($ch);
    var_dump($txt);
    return true;
}
//die;

send('192.168.1.205',9504 ,'{"id":1,"message":"test"}');
