<?php
include 'maze.php';
// $size=[20,15];
$size = [30, 20];
$ms = [];

$markX = [];
$markY = [];
$states = [];
function replaceMark($str, $x, $y)
{
    //explode(
    //for($a=0;$a<sizeof($str)
}
//$mstr = str_replace(PHP_EOL,"\r\n",$mstr);

$serv = new Swoole\Server("127.0.0.1", 1999);
$serv->on('Connect', function ($serv, $fd) {
    global $ms, $size;
    $ms[$fd] = new Maze($size[0], $size[1]);
    echo "Client: Connect.\n";
    $serv->send($fd, "welcome\r\n");
    $serv->send($fd, "\377\375\042\377\373\001"); //char mode
});
function recur_ksort(&$array)
{
    foreach ($array as &$value) {
        if (is_array($value)) {
            recur_ksort($value);
        }

    }
    return ksort($array);
}
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    global $size, $ms, $states;
    $maze = $ms[$fd];
    global $markX, $markY;
    if (!isset($markX[$fd])) {$markX[$fd] = 0;}
    if (!isset($markY[$fd])) {$markY[$fd] = 0;}
    $key = bin2hex($data);
/*
1b5b41
1b5b42
1b5b43
1b5b44

 */
//var_dump($maze->northDoor);
    //var_dump($maze->eastDoor);
    // print_r($maze->vertWalls);
    // print_r($maze->horWalls);
    // echo bin2hex($data);
    $my = $markY[$fd]; $mx = $markX[$fd];
// $dy=$size[1]-$my;$dx=$size[0]-$mx;
    $dy = $my; $dx = $mx;

    if ($key == '1b5b41') {
        //$serv->send($fd, "up");
        if ($markX[$fd] > 0 && isset($maze->horWalls[$dy][$dx - 1])) {
            $markX[$fd]--;
        }
    } elseif ($key == '1b5b42') {
        //$serv->send($fd, "down");
        if ($markX[$fd] < $size[1] && isset($maze->horWalls[$dy][$dx])) {
            $markX[$fd]++;
        }
    } elseif ($key == '1b5b43') {
        //$serv->send($fd, "right");
        if ($markY[$fd] < $size[0] && isset($maze->vertWalls[$dy + 1][$dx])) {
            $markY[$fd]++;
        }
    } elseif ($key == '1b5b44') {
        if ($markY[$fd] > 0 && isset($maze->vertWalls[$dy][$dx])) {
            $markY[$fd]--;
        }
        //$serv->send($fd, "left");
    }

    if ($mx == $size[0] - 1 && $my == $size[1] - 1) {
        $serv->send($fd, " *** CONGRATULATIONS: Password for next round: p2cks1A@@3k ***\r");

        $states[$fd] = true;
    }

    if (!isset($states[$fd])) {
        $serv->send($fd, $maze->printOut($markX[$fd], $markY[$fd]));

    }

});

$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->start();
