<?php

/**
 * https://github.com/shukean
 */

namespace YkCurls;
use YkCurls\Core as Core;

class Task{

    private $_mch;

    public function __construct($max_exec_ch_num = 0){
        $this->_mch = new Core\Fetcher($max_exec_ch_num);
    }

    public function addCurl($url, $post_data = '', array $headers = [], array $options = []){
        $ch_handler = new Core\TaskCurl($url);
        if (!empty($post_data)){
            $ch_handler->setPost($post_data);
        }
        if (!empty($headers)){
            $ch_handler->setHeader($headers);
        }
        if (!empty($options)){
            $ch_handler->setCurlOpts($options);
        }
        $this->_mch->addTask($ch_handler);
        return $ch_handler->getId();
    }

    public function execute(&$errno, &$error, array &$chs_errno = [], array &$ch_error = [], array &$http_info = []){
        $this->_mch->execute($errno, $error);
        $response = [];
        foreach ($this->_mch->getTasks() as $task){
            $id = $task->getId();
            $chs_errno[$id] = $task->getErrno();
            $chs_error[$id] = $task->getError();
            $response[$id] = $task->getResponse();
            $http_info[$id] = $task->getHttpInfo();
        }
        return $response;
    }


}