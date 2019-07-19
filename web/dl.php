<?php
if (!is_dir("dl")) {
    mkdir("dl");
}

if (isset($_GET["v"]) and strlen($_GET["v"]) == 11) {
    set_time_limit(0);
    if (!file_exists("dl/" . $_GET["v"] . ".mp4")) {
        exec("youtube-dl --format mp4 -o \"dl/" . $_GET["v"] . ".mp4\" \"" . $_GET["v"] . "\"");
    }
    header("Location: dl/" . $_GET["v"] . ".mp4");
}
die();
