<?php

namespace YkCurls\Core;

class TaskCallbackCurl extends TaskCurl implements ICallback{

    protected $_call_func;

    public function __construct($call, $url, $connect_timeout_ms = 300, $receive_timeout = 3){
        $this->_call_func = $call;
        parent::__construct($url, $connect_timeout_ms, $receive_timeout);
    }

    public function curlDone(){
        $func_name = $this->_call_func;
        $func_name($this);
    }

    public function setExtraArguments($k, $v){
        $this->extra_arguments[$k] = $v;
    }

    public function getExtraArguments($k){
        return array_key_exists($k, $this->extra_arguments) ? $this->extra_arguments[$k] : null;
    }

}