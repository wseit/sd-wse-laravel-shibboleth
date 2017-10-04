<?php

// this script will remove the require-dev section from composer.json

$filename = __DIR__.'/../../composer.json';

$content = file_get_contents($filename);

$data = json_decode($content, true);

unset($data['require-dev']);

$content = json_encode($data);

file_put_contents($filename, $content);
