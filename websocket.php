<?php
function redis() {
    static $redis;
    if (!isset($redis)) {
        try {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    return $redis;
}

define('PREFIX', 'bingo:log:');
$ws = new swoole_websocket_server('0.0.0.0', 9504);

$ws->on('open', function($ws, $request) {
    var_dump($request);
    $ws->push($request->fd, $request->fd . "hello welcome  \n");

});
$ws->on('message', function($ws, $frame) {
    $redis = redis();
    $data  = $frame->data;
    var_dump($data);
    $data_arr = json_decode($data, true);
    switch ($data_arr['type']) {
        case 1:
            $ws->push($frame->fd, 'pong');
            break;
        case 2:
            if ($data_arr['message']) {
                $redis->set(PREFIX . $frame->fd, 'client_id:' . $data_arr['message']);
                $redis->set(PREFIX . 'client_id:' . $data_arr['message'], $frame->fd);
            };
            $ws->push($frame->fd, 'client_id:' . $data_arr['message'] . ',上线了');
            break;
    }
});

$ws->on('request', function(swoole_http_request $request, swoole_http_response $response) {
    global $ws;//调用外部的server
    $body = $request->rawContent();
    $data = json_decode($body, true);
    if ($data['id']) {
        $redis = redis();
        $fd    = $redis->get(PREFIX . 'client_id:' . $data['id']);
        $ws->push($fd, $data['message']);
        $response->end("success");
    } else {
        foreach ($ws->connections as $fd) {
            $ws->push($fd, $data['message']);
        }
    }


});

$ws->on('close', function($ws, $fd) {
    echo "client-{$fd}  is closed";
});

$ws->start();

?>