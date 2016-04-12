<?php

/**
 * https://github.com/shukean
 */

namespace YkCurls\Core;

interface IGenerator{

    public function setExtraArguments($k, $v);

    public function getExtraArguments($k);

}