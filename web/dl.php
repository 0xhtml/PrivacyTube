<?php
if (!is_dir("../dl")) {
    mkdir("../dl");
}

if (isset($_GET["v"]) and strlen($_GET["v"]) == 11) {
    set_time_limit(0);
    if (!file_exists("../dl/" . $_GET["v"] . ".mp4")) {
        exec("youtube-dl --format mp4 -o \"../dl/" . $_GET["v"] . ".mp4\" \"" . $_GET["v"] . "\"");
    }
    $file_size = (string)(filesize("../dl/" . $_GET["v"] . ".mp4"));
    header("Content-Type: video/mp4");
    readfile("../dl/" . $_GET["v"] . ".mp4");
} elseif (isset($_GET["url"])) {
    $curl = curl_init($_GET["url"]);
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curl, $header) {
        header($header);
        return strlen($header);
    });
    curl_exec($curl);
    http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
    curl_close($curl);
}
die();
