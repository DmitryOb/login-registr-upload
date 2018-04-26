<?php
session_start();

//если поля в посте есть
if ( !empty($_POST['login']) && !empty($_POST['password']) 
	&& !empty($_POST['passwordretry']) && !empty($_POST['email'])){
		$mylogin = $_POST['login'];
		$mypass = $_POST['password'];
		$myemail = $_POST['email'];
		// если пароли не совпадают
		if ($_POST['password'] != $_POST['passwordretry']){
			$errors[] = 'Пароли не совпадают между собой';
		}
		// если имэйл неверный
		elseif (!filter_var($myemail, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Email указан некорректно';
		}
		// если пароли совпадают и имэйл верный то соединяемся с базой
		else {
			include 'db-connect.php';
			// делаем запрос и обрабатываем ответ
			$sql = " SELECT * FROM `users` WHERE login='$mylogin' ";
			$result = $mysql->query($sql); if (!$result) die($mysql->error);
			$data = $result->fetch_all(MYSQLI_ASSOC);
			// сначала проверяем есть ли такой логин в базе, если нет, то
			if (empty($data)) {
				//создаем пользователя
				$sql = "
				INSERT INTO `users` (`login`, `password`, `email`) values ('$mylogin', '$mypass', '$myemail')
				";
				$result = $mysql->query($sql); if (!$result) die($mysql->error);
				// делаем запрос в только что созданную запись и записываем в сессионную переменну, редиректим в index.php
				$sql = " SELECT * FROM `users` WHERE login='$mylogin' AND password='$mypass' ";
				$result = $mysql->query($sql); if (!$result) die($mysql->error);
				$data = $result->fetch_all(MYSQLI_ASSOC);
				if (!empty($data)) {
					$_SESSION['user'] = $data[0];
					header('Location: index.php');
				}
			}
			// если такой логин уже есть записываем в ошибку
			else { $errors[] = 'Такой пользователь уже есть'; };
		}

}
// если не все поля заполнены
else { $errors[] = 'Заполните все поля!'; }

?>


<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link href="style.css" rel="stylesheet" type="text/css" />
	<title>Страница Регистрации</title>
</head>
<body>
	<?php
		if (!empty($_SESSION['user'])) {
			echo 'Вы вошли как '.'<b>'.$_SESSION['user']['login'].'</b>'.
			'<a href="logout.php"><button>Выход</button></a>';
		}
		else echo '<p>Вы не авторизированы</p>';
	?>
	<hr>
	<div class="wrapper">
		<h2>Регистрация</h2>
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
				<label for="passwordretry">Повторите пароль</label>
				<input id="passwordretry" name="passwordretry">
			</div>
			<div>
				<label for="email">Email</label>
				<input id="email" name="email">
			</div>
			<div>
				<button type="submit">Регистрация</button>
			</div>
		</form>
		<?php
			// блок вывода ошибок
			if ($errors){
				foreach ($errors as $value) {
					echo '<p class="error">'.$value.'</p><br>';
				}
			}
			
		?>
		<a href="login.php">Страница Авторизации</a>
	</div>

</body>
</html>