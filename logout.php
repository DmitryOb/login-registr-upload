<?php
session_start();

session_destroy();

// в переменной location будет содержаться название страницы с которой мы пришли и затем редирект на неё
$from = $_SERVER['HTTP_REFERER'];
$index = strrpos($from, '/');
$location = substr($from, $index+1, strlen($from));
header("Location: $location");