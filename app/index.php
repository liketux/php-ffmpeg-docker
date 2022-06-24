<?php

$loader = include_once __DIR__.'/vendor/autoload.php';

$imageUrl = $_GET['url'] ?? null;
$sec = (int) ($_GET['sec'] ?? 1);

if (empty($imageUrl)) {
	http_response_code(400);
	exit;
}

$ext = pathinfo($imageUrl, PATHINFO_EXTENSION);

$file = sys_get_temp_dir() . '/' . uniqid() . '.' . $ext;
$img = sys_get_temp_dir() . '/' . uniqid() . '.jpeg';

if (!file_put_contents($file, file_get_contents($imageUrl))) {
	http_response_code(400);
	exit;
}

$ffmpeg = FFMpeg\FFMpeg::create();
$video = $ffmpeg->open($file);
$video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($sec))->save($img);

header('Content-type: image/jpeg');
readfile($img);

unlink($file);
unlink($img);
