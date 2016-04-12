<?php

function __autoload($name){
    $name = str_replace('\\', '/', $name);
    include __DIR__.'/../../'.$name.'.php';
}