<?php
// if(function_exists('shell_exec')) {
//     header('Content-Type: application/json');
//     echo shell_exec("youtube-dl -J https://www.youtube.com/watch?v=zGDzdps75ns");
// }

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

$myurl = $_POST["inputUrl"];

$yt = new YoutubeDl();
$collection = $yt->download(
    Options::create()
        ->downloadPath('./download')
        ->extractAudio(true)
        ->audioFormat('mp3')
        ->audioQuality('0') // best
        ->output('%(title)s.%(ext)s')
        ->url($myurl)
);

foreach ($collection->getVideos() as $video) {
    if ($video->getError() !== null) {
        echo "Error downloading video: {$video->getError()}.";
    } else {
        $video->getFile(); // audio file
    }
}

header('Location: index.php');