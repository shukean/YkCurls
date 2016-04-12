<?php

namespace YkCurls\Core;

class TaskGeneratorCurl extends TaskCurl implements IGenerator{

    private $extra_arguments = [];

    public function setExtraArguments($k, $v){
        $this->extra_arguments[$k] = $v;
    }

    public function getExtraArguments($k){
        return array_key_exists($k, $this->extra_arguments) ? $this->extra_arguments[$k] : null;
    }

}