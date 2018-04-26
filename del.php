<?php
// переменная с папкой текущего пользователя
$userfolder = 'userdata/'.$_POST['name'];

// перебираем всю папку пользователя
if ($handle = opendir($userfolder)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry!='.'&&$entry!='..'){
			//если среди всего списка есть совпадение то удаляем
			if(strstr($entry, $_POST['filename'])){
				$fullpath = $userfolder.'/'.$entry;
				unlink($fullpath); //удалить min файл
				unlink(current(explode("_min",$fullpath)).end(explode("_min",$fullpath))); // удалить полную версию
			}
		}
	}
	closedir($handle);
}

?>