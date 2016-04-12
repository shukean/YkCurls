<?php

include './conf.php';

$task = new YkCurls\Task();

echo $task->addCurl('http://test.baidu.com'), PHP_EOL;
echo $task->addCurl('http://www.baidu.com'), PHP_EOL;
echo $task->addCurl('http://www.nokkkkkkkkk.com'), PHP_EOL;       //retry 2 times

$chs_errno = $ch_error = $http_info = [];
$response = $task->execute($errno, $error, $chs_errno, $ch_error, $http_info);

// print_r($response);
// print_r($chs_errno);
print_r(array_keys($http_info));