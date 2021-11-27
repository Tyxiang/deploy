<?php
ini_set("display_errors", "Off");
date_default_timezone_set("Asia/Shanghai");
// main
$this_file_name_without_extension = basename(__FILE__, '.php'); // without extension

//// config
$config_file_name = $this_file_name_without_extension . '.json';
$config_json = file_get_contents($config_file_name);
if ($config_json == false) {
    exit('load config error!');
}

$config = json_decode($config_json, true);

//// jobs
$jobs = $config['jobs'];
$work_dir_name = $this_file_name_without_extension;
mkdir($work_dir_name);
foreach ($jobs as $key => $job) {
    echo '<p>';
    echo '---------- job ( ' . $key . ' ) ----------';
    // download
    $url_pathinfo = pathinfo($job['url']);
    $download_file_name = $url_pathinfo['basename']; // with extension
    $download_file_name_without_extension = $url_pathinfo['filename'];
    $download_file_extension = $url_pathinfo['extension'];
    $download_file_path = $work_dir_name . '/' . $download_file_name;
    echo '<p>';
    echo 'download: ' . $job['url'] . '<br>';
    echo 'to: ' . $download_file_path . '<br>';
    echo download($job['url'], $download_file_path);
    echo '</p>';
    // unzip if need
    if ($download_file_extension == 'zip') {
        $unzip_dir = $work_dir_name . '/' . $download_file_name_without_extension;
        // filename
        echo '<p>';
        echo 'unzip: ' . $download_file_path . '<br>';
        echo 'to: ' . $unzip_dir . '<br>';
        echo unzip($download_file_path, $unzip_dir);
        echo '</p>';
    }
    // set source and dest
    $source = $work_dir_name . '/' . $job['from'];
    $dest = $job['to'];
    // deploy
    // source must be exist
    if (!file_exists($source)) {
        echo "<p>";
        echo "form not exist";
        echo "</p>";
        continue;
    }
    if (is_dir($source)) {
        if ($job['clearfirst']) {
            echo '<p>';
            echo 'remove dest dir first <br>';
            echo remove_dir($dest);
            echo '</p>';
        }
        echo "<p>";
        echo "copy dir: " . $source . "<br>";
        echo "to: " . $dest . "<br>";
        echo copy_dir($source, $dest);
        echo "</p>";
    }
    if (is_file($source)) {
        if ($job['clearfirst']) {
            echo '<p>';
            echo 'remove dest file first <br>';
            echo remove_file($dest);
            echo '</p>';
        }
        echo "<p>";
        echo "copy file: " . $source . "<br>";
        echo "to: " . $dest . "<br>";
        echo copy_file($source, $dest);
        echo "</p>";
    }
    echo '</p>';
}
remove_dir( $work_dir_name );

// func
function download($url, $dir)
{
    $content = file_get_contents($url);
    if ($content === false) {
        return "file get contents error";
    }
    $r = file_put_contents($dir, $content);
    if ($r === false) {
        return "file put content error";
    }
    return "ok";
}

function unzip($path, $dir)
{
    $zipper = new ZipArchive;
    $r = $zipper->open($path);
    if ($r !== true) {
        return "open zip file error";
    }
    $r = $zipper->extractTo($dir);
    if ($r !== true) {
        return "extract zip file error";
    }
    $r = $zipper->close();
    if ($r !== true) {
        return "close zip file error";
    }
    return "ok";
}

function remove_dir($dir)
{
    if (!file_exists($dir)) {
        return "dir not exist";
    }
    $handle = opendir($dir);
    if ($handle === false) {
        return "opendir error";
    }
    while ($item = readdir($handle)) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        $this_file_name_without_extension = basename(__FILE__, '.php');
        if ($item == $this_file_name_without_extension) {
            continue;
        }
        if ($item == $this_file_name_without_extension . '.php') {
            continue;
        }
        if ($item == $this_file_name_without_extension . '.json') {
            continue;
        }
        $path = $dir . '/' . $item;
        if (is_file($path)) {
            $r = unlink($path);
            if ($r === false) {
                return "unlink error";
            }
        }
        if (is_dir($path)) {
            $r = remove_dir($path);
            if ($r !== "ok") {
                return "remove dir error";
            }
        }
    }
    closedir($handle);
    if ($dir != './') {
        $r = rmdir($dir);
        if ($r === false) {
            return "rmdir error";
        }
    }
    return "ok";
}

function copy_dir($source, $dest)
{
    if (!file_exists($dest)) {
        $r = mkdir($dest, 0755, true);
        if ($r === false) {
            return "mkdir error";
        }
    }
    $handle = opendir($source);
    if ($handle === false) {
        return "opendir error";
    }
    while ($item = readdir($handle)) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        $path_source = $source . '/' . $item;
        $path_dest = $dest . '/' . $item;
        if (is_file($path_source)) {
            $r = copy($path_source, $path_dest);
            if ($r === false) {
                return "copy error";
            }
        }
        if (is_dir($path_source)) {
            $r = copy_dir($path_source, $path_dest);
            if ($r !== "ok") {
                return "copy dir error";
            }
        }
    }
    closedir($handle);
    return "ok";
}

function remove_file($path)
{
    if (!file_exists($path)) {
        return "file not exist";
    }
    $r = unlink($path);
    if ($r === false) {
        return "unlink error";
    }
    return true;
}

function copy_file($source, $dest)
{
    $dest_dir = dirname($dest);
    if (!file_exists($dest_dir)) {
        $r = mkdir($dest_dir, 0755, true);
        if ($r === false) {
            return "mkdir error";
        }
    }
    $r = copy($source, $dest);
    if ($r === false) {
        return "copy error";
    }
    return "ok";
}
