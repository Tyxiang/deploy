<?php
/* 
file_path: a/b/c.php
dir_path: a/b
file_name: c.php
dir_name: b
file_name_main_part: c
file_name_extn_part: c
*/

ini_set("display_errors", "On");
date_default_timezone_set("Asia/Shanghai");
// main
$log_file_name = 'update.log';
$job_dir_name = 'job';
$config_file_name = 'config.json';
$log_file_name = "default.log";
//// config
$config_json = file_get_contents($config_file_name);
if ($config_json == false) {
    $msg = 'load config error';
    save_log($msg, $log_file_name);
    echo '<p>';
    echo $msg;
    echo '</p>';
    exit();
}
$config = json_decode($config_json, true);
//// creat work dir
@mkdir($job_dir_name);
//// jobs
$jobs = $config['jobs'];
foreach ($jobs as $key => $job) {
    echo '<p>';
    echo '---------- job ( ' . $key . ' ) ----------';
    $msg = do_job($job_dir_name, $job);
    save_log($msg, $log_file_name);
    echo '</p>';
}
//// delete work dir
//remove_dir($job_dir_name);

// func
function do_job($job_dir_name, $job) {
    $source = $job_dir_name . '/' . $job['copy'];
    $dest = $job['to'];
    // download
    $download_file_name = basename($job['download']); // with ext
    $download_file_path = $job_dir_name . '/' . $download_file_name;
    echo '<p>';
    echo 'download: ' . $job['download'] . '<br>';
    echo $r = download($job['download'], $download_file_path);
    echo '</p>';
    // unzip
    if ($job['unzip']) {
        $unzip_dir_path = $job_dir_name . '/' . 'source';
        $source = $unzip_dir_path . '/' . $job['copy'];
        echo '<p>';
        echo 'unzip: ' . $download_file_name . '<br>';
        echo $r = unzip($download_file_path, $unzip_dir_path);
        echo '</p>';
    }
    // remove_dest
    if ($job['clear_dest']) {
        echo '<p>';
        echo 'remove: ' . $job['to'] . '<br>';
        if (is_dir($source)) echo $r = remove_dir($dest);
        if (is_file($source)) echo $r = remove_file($dest);
        echo '</p>';
    }
    // copy
    echo "<p>";
    echo "copy: " . $source . "<br>";
    echo "to: " . $dest . "<br>";
    if (is_dir($source)) echo $r = copy_dir($source, $dest);
    if (is_file($source)) echo $r = copy_file($source, $dest);
    echo "</p>";
    // return
    return $r;
}

function save_log($msg, $log_file_name) {
    $item = '[ ' . date('Y-m-d H:i:s') . ' ] ' . $msg . PHP_EOL;
    $r = file_put_contents($log_file_name, $item, FILE_APPEND);
    if ($r === false) return "file put content error";
    return 'ok';
}

function download($url, $dir)
{
    $content = file_get_contents($url);
    if ($content === false) return "file get contents error";
    $r = file_put_contents($dir, $content);
    if ($r === false) return "file put content error";
    return "ok";
}

function unzip($path, $dir)
{
    $zipper = new ZipArchive;
    $r = $zipper->open($path);
    if ($r !== true) return "open zip file error";
    $r = $zipper->extractTo($dir);
    if ($r !== true) return "extract zip file error";
    $r = $zipper->close();
    if ($r !== true) return "close zip file error";
    return "ok";
}

function remove_dir($dir)
{
    if (!file_exists($dir)) return "dir not exist";
    foreach (glob($dir . '/*') as $item) {
        $this_dir_path = dirname(__FILE__);
        if (realpath($item) == $this_dir_path) continue;
        if (is_file($item)) {
            $r = unlink($item);
            if ($r === false) return "unlink error";
        } else {
            $r = remove_dir($item);
            if ($r !== "ok") return "remove dir error";
        }
    }
    $r = rmdir($dir);
    if ($r === false) return "rmdir error";
    return "ok";
}

function copy_dir($source, $dest)
{
    if (!file_exists($dest)) {
        $r = mkdir($dest, 0755, true);
        if ($r === false) return "mkdir error";
    }
    $handle = opendir($source);
    if ($handle === false) return "opendir error";
    while ($item = readdir($handle)) {
        if ($item == '.' || $item == '..') continue;
        $path_source = $source . '/' . $item;
        $path_dest = $dest . '/' . $item;
        if (is_file($path_source)) {
            $r = copy($path_source, $path_dest);
            if ($r === false) return "copy error";
        }
        if (is_dir($path_source)) {
            $r = copy_dir($path_source, $path_dest);
            if ($r !== "ok") return "copy dir error";
        }
    }
    closedir($handle);
    return "ok";
}

function remove_file($path)
{
    if (!file_exists($path)) return "file not exist";
    $r = unlink($path);
    if ($r === false) return "unlink error";
    return true;
}

function copy_file($source, $dest)
{
    $dest_dir = dirname($dest);
    if (!file_exists($dest_dir)) {
        $r = mkdir($dest_dir, 0755, true);
        if ($r === false) return "mkdir error";
    }
    $r = copy($source, $dest);
    if ($r === false) return "copy error";
    return "ok";
}