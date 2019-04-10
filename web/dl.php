<?php
if (!is_dir("../dl")) {
    mkdir("../dl");
}

if (isset($_GET["v"]) and strlen($_GET["v"]) == 11) {
    if (!file_exists("../dl/" . $_GET["v"] . ".mp4")) {
        set_time_limit(0);
        exec("youtube-dl --format mp4 -o \"../dl/" . $_GET["v"] . ".mp4\" \"" . $_GET["v"] . "\"");
    }
    $file_size = (string)(filesize("../dl/" . $_GET["v"] . ".mp4"));
    header("Content-Type: video/mp4");
    header("Accept-Ranges: bytes");
    header("Content-Length: $file_size");
    header("Content-Disposition: inline;");
    header("Content-Range: bytes .$file_size");
    header("Content-Transfer-Encoding: binary\n");
    header("Connection: close");
    readfile("../dl/" . $_GET["v"] . ".mp4");
} elseif (isset($_GET["url"])) {
    $curl = curl_init($_GET["url"]);
    curl_exec($curl);
    curl_close($curl);
}
die();
