<?php
session_start();

//соединяемся с базой
include 'db-connect.php';
// создаем таблицу users если она еще не создана
$sql = "
CREATE TABLE IF NOT EXISTS `users` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`login` TINYTEXT COLLATE utf8_bin NOT NULL ,
	`password` TINYTEXT COLLATE utf8_bin NOT NULL ,
	`email` TINYTEXT COLLATE utf8_bin NOT NULL ,
	PRIMARY KEY (`id`)
	)
	ENGINE = InnoDB DEFAULT CHARSET=utf8;
";
$result = $mysql->query($sql); if (!$result) die($mysql->error);
// если сессионной перменной нет то редирект на страницу авторизации login.php
if (empty($_SESSION['user'])) {
	header('Location: login.php');
};

$username = $_SESSION['user']['login'];
$userfolder = 'userdata/'.$username;
// если папка есть то сканируем её
if (file_exists($userfolder)){
	$file_list = scandir($userfolder);
};

?>

<!doctype html>
<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<link href="style.css" rel="stylesheet" type="text/css" />
		<title>Главная</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<link rel="stylesheet" href="fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
	</head>
	<body>
		<hr>
		<?=
			'<p>Вы вошли как '.'<b class="name">'.$_SESSION['user']['login'].'</b></p>';
		?>
		<a href="logout.php"><button>Выход</button></a><br>
		<a href="registration.php">Регистрация</a>
		<hr>
		<form method="post" action="upload.php" enctype="multipart/form-data">
			<input type="file" name="image" />
			<input type="submit" value="загрузить" />
		</form>
		<br>
		<?php
			// если папка пользователя не пустая
			if (!empty($file_list)){
				foreach ($file_list as $value) {
					// то перебираем её в поиске минифицированной версии файла и выводим
					if(strstr($value, '_min')){
						$min_src = $userfolder.'/'.$value;
						$before_min = stristr($min_src, '_min', true);
						$file_ext = end(explode('.', $min_src));
						$max_src = $before_min.'.'.$file_ext;
						echo "
						<div class='myImage' >
							<a class='popup' href='$max_src'>
								<img src='$min_src'>
							</a>
							<img class ='del' src='fancybox/recycling-bin.png'>
						</div>
						";
					}
				};
			}
		?>
	<script type="text/javascript">
		// fancybox для попапа
		$(document).ready(function() {
			$(".popup").fancybox();
		});

		// на все button слушаем клик
		document.querySelectorAll('img.del').forEach((e,i)=>{
			e.addEventListener('click', (e)=>{
				var img = e.target.previousElementSibling.firstElementChild;
				var src = img.src;
				var begin = src.lastIndexOf("/");
				var end = src.lastIndexOf(".");
				var filename = src.slice(begin+1, end);
				var name = document.querySelector('.name').textContent;
				// передаем POST в файл del.php с данными по какой картинке клик и от какого пользователя
				fetch('del.php', {
					method: 'post',
					headers: {
						"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
					},
					body: 'filename='+filename+'&'+'name='+name
					})
				.then()
				.then( reponse => reponse.text().then( (data)=> {
					// после успешного удаления на сервере файлов так же удаляем сам DOM элементы
					function removeDummy(elem) {
						var elem = elem;
						elem.parentNode.removeChild(elem);
						return false;
					};
					removeDummy(img);
					e.target.style.display = 'none';
				}))
				.catch((error) => console.log('Request failed', error));
			})
		})
	</script>
	</body>
</html>