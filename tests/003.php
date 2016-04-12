<?php

include './conf.php';

$task = new YkCurls\TaskGenerator();

echo $task->addCurl('http://www.baidu.com'), PHP_EOL;
echo $task->addCurl('http://127.0.0.1'), PHP_EOL;


foreach ($task->execute($errno, $error) as $t){
    var_dump($t->getId());
}