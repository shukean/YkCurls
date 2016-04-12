<?php

/**
 * https://github.com/shukean
 */

namespace YkCurls;

class TaskCallback{

    private $_mch;

    public function __construct($max_exec_ch_num = 0){
        $this->_mch = new Core\Fetcher($max_exec_ch_num);
    }

    public function addCurl(callable $func, $url, $post_data = '', array $headers = [], array $options = []){
        $ch_handler = new Core\TaskCallbackCurl($func, $url);
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

    public function execute(&$errno, &$error){
        $this->_mch->execute($errno, $error);
    }


}