<?php

    $loader = include_once __DIR__.'/vendor/autoload.php';

    $imageUrl = $_GET['url'] ?? null;
    $sec = (int) ($_GET['sec'] ?? 0);

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

    if (!file_exists($img)) {
        http_response_code(400);
        exit;
    }
    $info = $video->getFormat()->all();

    $result = [
        'frame' => base64_encode(file_get_contents($img)),
        'info' => $info,
    ];

    unlink($file);
    unlink($img);

    print json_encode($result);
