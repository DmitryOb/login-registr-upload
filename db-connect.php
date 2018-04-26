<?php
//соединяемся с базой
$base = 'myfirstbd';
$mysql = @new mysqli('localhost', 'mysql', 'mysql', $base); // подключаемся к базе
if (mysqli_connect_errno()){die(mysqli_connect_error());}; //проверка на ошибки и закрытие скрипта в случае error
$sql = "set names 'utf8'"; // задаем кодировку
$result = $mysql->query($sql); // посылаем запрос в переменой $result на случай ошибки запроса
if (!$result) die($mysql->error);
?>