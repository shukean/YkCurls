<?php

include './conf.php';

$task = new YkCurls\TaskCallback();

function bd_func(YkCurls\Core\TaskCallbackCurl $task){
    echo $task->getId(), PHP_EOL;
}

function local_func(YkCurls\Core\TaskCallbackCurl $task){
    echo $task->getId(), PHP_EOL;
}


echo $task->addCurl('bd_func', 'http://www.baidu.com'), PHP_EOL;
echo $task->addCurl('local_func', 'http://127.0.0.1'), PHP_EOL;

$task->execute($errno, $error);