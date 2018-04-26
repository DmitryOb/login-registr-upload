<?php
session_start();

// если есть авторизированная сессия то переходим на index.php
if (!empty($_SESSION['user'])) {
	header('Location: index.php');
}

// если есть данные из POST
if (!empty($_POST['login']) && !empty($_POST['password'])) {

	//соединяемся с базой
	include 'db-connect.php';

	// делаем запрос и обрабатываем ответ
	$mylogin = $_POST['login'];
	$mypass = $_POST['password'];
	$sql = " SELECT * FROM `users` WHERE login='$mylogin' AND password='$mypass' ";
	$result = $mysql->query($sql); if (!$result) die($mysql->error); // отправляет запрос
	$data = $result->fetch_all(MYSQLI_ASSOC); // получает ответ на запрос

	// если данные из инпутов верные
	if (!empty($data)) {
		// создаем сессионную перменную со строкой из таблицы mysql
		$_SESSION['user'] = $data[0];
		// редирект в index.php и остановка скрипта
		header('Location: index.php');
		die;
	}
	$errors = 'Неверный логин или пароль';
}
?>

<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<link href="style.css" rel="stylesheet" type="text/css" />
	<title>Вход</title>
</head>
<body>

<?php if (!empty($errors)) echo $errors ?>
	<div class="wrapper">
		<h2>Авторизация</h2>
		<form method="POST" class="form">
			<div>
				<label for="login">Логин</label>
				<input id="login" name="login">
			</div>
			<div>
				<label for="password">Пароль</label>
				<input id="password" name="password">
			</div>
			<div>
				<button type="submit">Вход</button>
			</div>
		</form>
		<br><br>
		<a href="registration.php">Еще не зарегистрированы?</a>
	</div>
</body>
</html>