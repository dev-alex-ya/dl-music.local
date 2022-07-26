<?php

// print_r($_GET);

// if(isset($_GET['filename']))
// {
//     $dir = './download';
//     //Read the url
//     $filename = $_GET['filename'];

//     //Clear the cache
//     clearstatcache();

//     //Check the file path exists or not
//     if(file_exists("$dir/$url")) {

//         //Define header information
//         header('Content-Description: File Transfer');
//         header('Content-Type: audio/mpeg');
//         header('Content-Disposition: attachment; filename="'.basename($url).'"');//inline - играет в браузере
//         header('Content-Length: ' . filesize(basename("$dir/$url")));
//         header('Cache-Control: no-cache');
//         header("Content-Transfer-Encoding: binary");

//         //Clear system output buffer
//         flush();

//         //Read the size of the file
//         readfile("$dir/$url", true);

//         //Terminate from the script
//         die();
//     }
//     else {
//         echo "File path does not exist.";
//     }
// }
// echo "File path is not defined.";

/////////////////////////////////////////////////////////////////////////


function smartReadFile($location, $filename, $mimeType = 'application/octet-stream')
{
    if (!file_exists($location)) {
        header("HTTP/1.0 404 Not Found");
        return;
    }

    $size = filesize($location);
    $time = date('r', filemtime($location));

    $fm = @fopen($location, 'rb');
    if (!$fm) {
        header("HTTP/1.0 505 Internal server error");
        return;
    }

    $begin = 0;
    $end = $size;

    if (isset($_SERVER['HTTP_RANGE'])) {
        if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
            $begin = intval($matches[0]);
            if (!empty($matches[1]))
                $end = intval($matches[1]);
        }
    }

    if ($begin > 0 || $end < $size)
    header('HTTP/1.0 206 Partial Content');
    else
    header('HTTP/1.0 200 OK');

    header("Content-Type: $mimeType");
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Accept-Ranges: bytes');
    header('Content-Length:' . ($end - $begin));
    header("Content-Range: bytes $begin-$end/$size");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Transfer-Encoding: binary\n");
    header("Last-Modified: $time");
    header('Connection: close');

    $cur = $begin;
    fseek($fm, $begin, 0);

    while (!feof($fm) && $cur < $end && (connection_status() == 0)) {
        print fread($fm, min(1024 * 16, $end - $cur));
        $cur += 1024 * 16;
    }
}

$dir = './download';
$filename = $_GET['filename'];
// $path = basename("$dir/$filename");

// echo "$dir/$filename";

smartReadFile("$dir/$filename", $filename, "audio/mpeg");

header("Location: index.php");