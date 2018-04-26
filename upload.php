<?php
session_start();
// функция для обрезки изображения
function cropImage($aInitialImageFilePath, $aNewImageFilePath, $aNewImageWidth, $aNewImageHeight) {
	if (($aNewImageWidth < 0) || ($aNewImageHeight < 0)) {
		return false;
	}
	// Массив с поддерживаемыми типами изображений
	$lAllowedExtensions = array(1 => "gif", 2 => "jpeg", 3 => "png", 4 => "jpg");
	// Получаем размеры и тип изображения в виде числа
	list($lInitialImageWidth, $lInitialImageHeight, $lImageExtensionId) = getimagesize($aInitialImageFilePath); 
	if (!array_key_exists($lImageExtensionId, $lAllowedExtensions)) {
		return false;
	}
	$lImageExtension = $lAllowedExtensions[$lImageExtensionId];
	// Получаем название функции, соответствующую типу, для создания изображения
	$func = 'imagecreatefrom' . $lImageExtension;
	// Создаём дескриптор исходного изображения
	$lInitialImageDescriptor = $func($aInitialImageFilePath);
	// Определяем отображаемую область
	$lCroppedImageWidth = 0;
	$lCroppedImageHeight = 0;
	$lInitialImageCroppingX = 0;
	$lInitialImageCroppingY = 0;
	if ($aNewImageWidth / $aNewImageHeight > $lInitialImageWidth / $lInitialImageHeight) {
		$lCroppedImageWidth = floor($lInitialImageWidth);
		$lCroppedImageHeight = floor($lInitialImageWidth * $aNewImageHeight / $aNewImageWidth);
		$lInitialImageCroppingY = floor(($lInitialImageHeight - $lCroppedImageHeight) / 2);
	} else {
		$lCroppedImageWidth = floor($lInitialImageHeight * $aNewImageWidth / $aNewImageHeight);
		$lCroppedImageHeight = floor($lInitialImageHeight);
		$lInitialImageCroppingX = floor(($lInitialImageWidth - $lCroppedImageWidth) / 2);
	}
	// Создаём дескриптор для выходного изображения
	$lNewImageDescriptor = imagecreatetruecolor($aNewImageWidth, $aNewImageHeight);
	imagecopyresampled($lNewImageDescriptor, $lInitialImageDescriptor, 0, 0, $lInitialImageCroppingX, $lInitialImageCroppingY, $aNewImageWidth, $aNewImageHeight, $lCroppedImageWidth, $lCroppedImageHeight);
	$func = 'image' . $lImageExtension;
	// сохраняем полученное изображение в указанный файл
	return $func($lNewImageDescriptor, $aNewImageFilePath);
};

$username = $_SESSION['user']['login'];
$userfolder = 'userdata/'.$username;
if ( isset($_FILES['image']) ){
	$errors = array();
	$file_name = $_FILES['image']['name'];
	$file_size = $_FILES['image']['size'];
	$file_tmp = $_FILES['image']['tmp_name'];
	$file_type = $_FILES['image']['type'];
	// расширение файла
	$file_ext = strtolower(end(explode('.', $file_name)));
	if ($file_size<1){
		$errors[] = 'Пустой файл';
	}
	// если нет ошибок
	if ( empty($errors) ){
		$path_to_file = $userfolder.'/'.$file_name; //'userdata/admin' '/' 'treugolnik_cveta.png'
		$path_to_new_file = current(explode('.', $path_to_file)).'_min.'.$file_ext;
		// если папки не существует то создать её и загрузить туда изображение

		if (!file_exists($userfolder)) {
			mkdir($userfolder, 0777, true);
			move_uploaded_file($file_tmp, $path_to_file);
			cropImage($path_to_file, $path_to_new_file, 100, 100);
		} else {
			// заливаем оригинальный файл в папку пользователя
			move_uploaded_file($file_tmp, $path_to_file);
			cropImage($path_to_file, $path_to_new_file, 100, 100);
		}
	} else {
		print_r($errors);
	};
}

// в переменной location будет содержаться название страницы с которой мы пришли и затем редирект на неё
$from = $_SERVER['HTTP_REFERER'];
$index = strrpos($from, '/');
$location = substr($from, $index+1, strlen($from));
header("Location: $location");
?>