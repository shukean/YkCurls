<?php

/**
 * https://github.com/shukean
 */

namespace YkCurls\Core;

class Fetcher{

    /**
     * @var curl_multi handler
     */
    private $_mch;
    /**
     * @var TaskCurl
     */
    private $_chs = [];
    /**
     * @var max exec ch number as same time
     */
    private $_max_exec_ch_num = 0;
    /**
     * @var call log funtion, arguments like: line, file, message, code, extra_array
     */
    private $_logger = null;
    private $_task_wait_pool = [];

    public function __construct($max_exec_ch_num = 0, callable $logger = null){
        $this->_mch = curl_multi_init();
        $this->_max_exec_ch_num = $max_exec_ch_num;
        $this->_logger = $logger;
    }

    public function trace_log($line, $file, $message, $code, array $extra = []){
        if (($logger = $this->_logger) !== null){
            $logger($line, $file, $message, $code, $extra);
        }
    }

    public function setMultiOpt($option, $value){
        curl_multi_setopt($this->_mch, $option, $value);
    }

    public function addTask(TaskCurl $task){
        if (!$this->_max_exec_ch_num || count($this->_chs) < $this->_max_exec_ch_num){
            curl_multi_add_handle($this->_mch, $task->getCh());
        }else{
            $this->_task_wait_pool[$task->getId()] = $task;
        }
        $this->_chs[$task->getId()] = $task;
    }

    public function getTasks(){
        return $this->_chs;
    }

    public function getTaskNum(){
        return count($this->_chs);
    }

    public function execute(&$errno, &$error){
        $this->executeCallback($errno, $error);
    }

    public function executeCallback(&$errno, &$error){
        $active = null;
        do{
RESET:
            do {
                $mrc = curl_multi_exec($this->_mch, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            if ($mrc != CURLM_OK){
                break;
            }

            $info = curl_multi_info_read($this->_mch, $msgs_in_queue);
            if ($info !== false){
                $ch = $info['handle'];
                $index = (string) $ch;
                $task = $this->_chs[$index];
                if ($info['result'] == CURLE_OK || !$task->connErrorRetryEnable()){
                    $task->setCurlExecInfo($info);
                    if ($task instanceof ICallback){
                        $task->curlDone();
                    }
                    curl_multi_remove_handle($this->_mch, $ch);
                    curl_close($ch);
                    if (!empty($this->_task_wait_pool)){
                        $wait_task = array_shift($this->_task_wait_pool);
                        curl_multi_add_handle($this->_mch, $wait_task->getCh());
                        goto RESET;
                    }
                }else{
                    $this->trace_log(__LINE__, __FILE__, 'curl info read fail', $info['result'], $info);
                    curl_multi_remove_handle($this->_mch, $ch);
                    curl_multi_add_handle($this->_mch, $ch);
                    goto RESET;
                }
            }

            if ($active > 0) {
                curl_multi_select($this->_mch, 0.5);
            }

        }while ($active);
        $errno = $mrc;
        $error = curl_multi_strerror($errno);
        curl_multi_close($this->_mch);
    }

    public function executeGenerator(&$errno, &$error){
        $active = null;
        do{
RESET:
            do {
                $mrc = curl_multi_exec($this->_mch, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            if ($mrc != CURLM_OK){
                break;
            }

            $info = curl_multi_info_read($this->_mch, $msgs_in_queue);
            if ($info !== false){
                $ch = $info['handle'];
                $index = (string) $ch;
                $task = $this->_chs[$index];
                if ($info['result'] == CURLE_OK || !$task->connErrorRetryEnable()){
                    $task->setCurlExecInfo($info);
                    if ($task instanceof IGenerator){
                        yield $task;
                    }
                    curl_multi_remove_handle($this->_mch, $ch);
                    curl_close($ch);
                    if (!empty($this->_task_wait_pool)){
                        $wait_task = array_shift($this->_task_wait_pool);
                        curl_multi_add_handle($this->_mch, $wait_task->getCh());
                        goto RESET;
                    }
                }else{
                    $this->trace_log(__LINE__, __FILE__, 'curl info read fail', $info['result'], $info);
                    curl_multi_remove_handle($this->_mch, $ch);
                    curl_multi_add_handle($this->_mch, $ch);
                    goto RESET;
                }
            }

            if ($active > 0) {
                curl_multi_select($this->_mch, 0.5);
            }

        }while ($active);
        $errno = $mrc;
        $error = curl_multi_strerror($errno);
        curl_multi_close($this->_mch);
    }

}