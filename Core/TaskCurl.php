<?php

/**
 * https://github.com/shukean
 */

namespace YkCurls\Core;

class TaskCurl{

    private $_ch;
    private $_ch_key;
    private $_conn_error_retry_times = 2;
    private $_conn_times = 0;
    private $_opts = [];

    private $_errno;
    private $_error;
    private $_response;
    private $_http_info;

    public function __construct($url, $connect_timeout_ms = 300, $receive_timeout = 3){
        $this->_ch = curl_init($url);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, false);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_TIMEOUT, $receive_timeout);
        if ($connect_timeout_ms > 0){
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT_MS, $connect_timeout_ms);
            if ($connect_timeout_ms < 1000){
                curl_setopt($this->_ch, CURLOPT_NOSIGNAL, true);
            }
        }
        $this->_ch_key = (string) $this->_ch;
    }

    public function getErrno(){
        return $this->_errno;
    }

    public function getError(){
        return $this->_error;
    }

    public function getResponse(){
        return $this->_response;
    }

    public function getHttpInfo(){
        return $this->_http_info;
    }

    public function getId(){
        return $this->_ch_key;
    }

    public function getCh(){
        return $this->_ch;
    }

    public function setConnErrorRetryTimes($times = 1){
        $this->_conn_error_retry_times = intval($times);
    }

    public function connErrorRetryEnable(){
        $this->_conn_times ++;
        return $this->_conn_times < $this->_conn_error_retry_times;
    }

    public function setPost($post_data){
        $this->setCurlOpt($this->_ch, CURLOPT_POST, true);
        $this->setCurlOpt($this->_ch, CURLOPT_POSTFIELDS, $post_data);
    }

    public function setHeader(array $headers){
        $this->setCurlOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function setCurlOpt($option, $value){
        curl_setopt($this->_ch, $option, $value);
        $this->_opts[$option] = $value;
    }

    public function setCurlOpts(array $options){
        curl_setopt_array($this->_ch, $options);
        foreach ($options as $option => $value){
            $this->_opts[$option] = $value;
        }
    }

    public function getCurlOptValue($key){
        return isset($this->_opts[$key]) ? $this->_opts[$key] : null;
    }

    public function setCurlExecInfo($info){
        $this->_errno = $info['result'];
        $this->_error = curl_error($info['handle']); //curl_strerror($this->_errno);
        $this->_response = curl_multi_getcontent($info['handle']);
        $this->_http_info = curl_getinfo($info['handle']);
    }

}