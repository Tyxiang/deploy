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
$config_file_name = 'config.json';
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
//// jobs
$jobs = $config['jobs'];
foreach ($jobs as $key => $job) {
    echo '<p>';
    echo '---------- job ( ' . $key . ' ) ----------';
    echo '<p>';
    $msgs = do_job($job);
    foreach($msgs as $msg){
        echo $msg;
        echo '<br>';
        save_log($msg, $log_file_name);
    }
    echo '</p>';
    echo '</p>';
}
//// del temp
echo '<p>';
echo '---------- clear ----------';
foreach ($jobs as $job) {
    $msgs = do_clear($job);
    foreach($msgs as $msg){
        save_log($msg, $log_file_name);
    }
}
echo '</p>';

// func
//// app
function do_job($job) {
    $r = [];
    // job name
    $job_name = md5($job['download']);
    // download
    $download_file_name = $job_name . '.tmp'; 
    if (file_exists($download_file_name)) {
        $msg = 'file already exist';
    } else {
        $msg = download($job['download'], $download_file_name);
    }
    $r[] = 'download ' . $job['download'] . ' ... ' . $msg;
    // unzip
    $unzip_dir_name = $job_name;
    if (file_exists($unzip_dir_name)) {
        $msg = 'unzip dir already exist';
    } else {
        $msg = unzip($download_file_name, $unzip_dir_name);
    }
    $r[] = 'unzip ... ' . $msg;
    // set source and dest
    $source = $unzip_dir_name . '/' . $job['copy'];
    $dest = $job['to'];
    // clear_dest
    if ($job['clear_dest']) {
        if (file_exists($job['to'])) {
            if (is_dir($source)) $msg = remove_dir($dest);
            if (is_file($source)) $msg = remove_file($dest);
        } else {
            $msg = 'not exist';
        }
    }
    $r[] = 'clear dest ' . $job['to'] . ' ... ' . $msg;
    // copy
    if (is_dir($source)) $msg = copy_dir($source, $dest);
    if (is_file($source)) $msg = copy_file($source, $dest);
    $r[] = 'copy ' . $job['copy'] . ' to ' . $job['to'] .' ... ' . $msg;
    // return
    return $r;
}

function do_clear($job) {
    $r = [];
    $job_name = md5($job['download']);
    // del download file
    $download_file_name = $job_name . '.tmp'; 
    if (file_exists($download_file_name)) {
        $msg = remove_file($download_file_name);
        $r[] = 'remove download file ... ' . $msg;
    }
    // del unzip dir
    $unzip_dir_name = $job_name;
    if (file_exists($unzip_dir_name)) {
        $msg = remove_dir($unzip_dir_name);
        $r[] = 'remove unzip dir ... ' . $msg;
    }
    return $r;
}

//// core
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
    @rmdir($dir);
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
        $source_path = $source . '/' . $item;
        $dest_path = $dest . '/' . $item;
        if (is_file($source_path)) {
            $r = copy($source_path, $dest_path);
            if ($r === false) return "copy error";
        }
        if (is_dir($source_path)) {
            $r = copy_dir($source_path, $dest_path);
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
    return 'ok';
}

function copy_file($source, $dest)
{
    $dest_dir_path = dirname($dest);
    if (!file_exists($dest_dir_path)) {
        $r = mkdir($dest_dir_path, 0755, true);
        if ($r === false) return "mkdir error";
    }
    $r = copy($source, $dest);
    if ($r === false) return "copy error";
    return "ok";
}

function save_log($msg, $log_file_name) {
    $item = '[ ' . date('Y-m-d H:i:s') . ' ] ' . $msg . PHP_EOL;
    $r = file_put_contents($log_file_name, $item, FILE_APPEND);
    if ($r === false) return "file put content error";
    return 'ok';
}