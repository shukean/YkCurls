<?php

/**
 * https://github.com/shukean
 */

namespace YkCurls\Core;

interface ICallback{

    public function curlDone();

    public function setExtraArguments($k, $v);

    public function getExtraArguments($k);

}