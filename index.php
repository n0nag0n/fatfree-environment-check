<?php

// this is an example of how to implement this environment check
include(__DIR__.'/vendor/autoload.php');

$fw = Base::instance();

n0nag0n\Environment_Check::instance();

$fw->run();