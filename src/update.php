<?php
/* 
file_path: a/b/c.php
dir_path: a/b
file_name: c.php
dir_name: b
file_name_main_part: c
file_name_extn_part: c
*/

ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');
// main
$this_file_name_main_part = basename(__FILE__, '.php');
$this_file_name = $this_file_name_main_part . '.php';
$config_file_name = $this_file_name_main_part . '.json';
$log_file_name = $this_file_name_main_part . '.log';
//// config
$config_json = file_get_contents($config_file_name);
if ($config_json == false) {
    $msg = 'load config error!';
    save_log($msg, $log_file_name);
    echo '<p>';
    echo $msg;
    echo '</p>';
    exit();
}
$config = json_decode($config_json, true);

//// jobs
$jobs = $config['jobs'];
$protects = $config['protect'];
foreach ($jobs as $key => $job) {
    echo '<p>';
    echo '---------- job ( ' . $key . ' ) ----------';
    echo '<p>';
    $msgs = do_job($job);
    foreach($msgs as $msg){
        save_log($msg, $log_file_name);
        echo $msg;
        echo '<br>';
    }
    echo '</p>';
    echo '</p>';
}
//// del temp
echo '<p>';
echo '----------- clear -----------';
foreach ($jobs as $job) {
    $msgs = do_clear_temp($job);
    foreach($msgs as $msg){
        save_log($msg, $log_file_name);
        echo $msg;
        echo '<br>';
    }
}
echo '</p>';

// func
//// app
function do_job($job) {
    $r = [];
    // job name
    $job_name = md5($job['download']);
    $download_file_name = $job_name . '.tmp'; 
    $unzip_dir_name = $job_name;
    $clear_path = $job['clear'];
    $source_path = $unzip_dir_name . '/' . $job['copy'];
    $dest_path = $job['to'];
    // clear dest
    // Clean should be in front, otherwise the downloaded content will be deleted when the dest is './'
    if ($clear_path != '') {
        if (file_exists($clear_path)) {
            if (is_dir($clear_path)) $msg = remove_dir($clear_path);
            if (is_file($clear_path)) $msg = remove_file($clear_path);
        } else {
            $msg = 'dest not exist!';
        }
        $r[] = 'clear "' . $job['clear'] . '" ---> ' . $msg;
    }
    // download
    if ($download_file_name != '') {
        if (file_exists($download_file_name)) {
            $msg = 'file already exist!';
        } else {
            $msg = download($job['download'], $download_file_name);
        }
        $r[] = 'download "' . $job['download'] . '" ---> ' . $msg;
    }
    // unzip
    if (file_exists($download_file_name)){
        if (file_exists($unzip_dir_name)) {
            $msg = 'unzip dir already exist!';
        } else {
            $msg = unzip($download_file_name, $unzip_dir_name);
        }
        $r[] = 'unzip ---> ' . $msg;
    }
    // copy
    if ($source_path != '' && $dest_path != '') {
        if (file_exists($source_path)) {
            if (is_dir($source_path)) $msg = copy_dir($source_path, $dest_path);
            if (is_file($source_path)) $msg = copy_file($source_path, $dest_path);
        }else{
            $msg = 'source not exist!';
        }
        $r[] = 'copy "' . $job['copy'] . '" to "' . $job['to'] . '" ---> ' . $msg;
    }
    // return
    return $r;
}

function do_clear_temp($job) {
    $r = [];
    $job_name = md5($job['download']);
    // del download file
    $download_file_name = $job_name . '.tmp'; 
    if (file_exists($download_file_name)) {
        $msg = remove_file($download_file_name);
        $r[] = 'remove download file ---> ' . $msg;
    }
    // del unzip dir
    $unzip_dir_name = $job_name;
    if (file_exists($unzip_dir_name)) {
        $msg = remove_dir($unzip_dir_name);
        $r[] = 'remove unzip dir ---> ' . $msg;
    }
    // return
    return $r;
}

//// core
function download($url, $dir)
{
    $content = file_get_contents($url);
    if ($content === false) return 'file get contents error!';
    $r = file_put_contents($dir, $content);
    if ($r === false) return 'file put content error!';
    return 'ok.';
}

function unzip($path, $dir)
{
    $zipper = new ZipArchive;
    $r = $zipper->open($path);
    if ($r !== true) return 'open zip file error!';
    $r = $zipper->extractTo($dir);
    if ($r !== true) return 'extract zip file error!';
    $r = $zipper->close();
    if ($r !== true) return 'close zip file error!';
    return 'ok.';
}

function remove_dir($dir)
{
    if (!file_exists($dir)) return 'dir not exist!';
    // protect
    global $protects;
    foreach ($protects as $protect) {
        if (realpath($dir) == realpath($protect)) return 'ok.';
    }
    // 
    foreach (glob($dir . '/*') as $item) {
        if (is_file($item)) {
            $r = remove_file($item);
            if ($r !== 'ok.') return 'remove file error!';
        } else {
            $r = remove_dir($item);
            if ($r !== 'ok.') return 'remove dir error!';
        }
    }
    @rmdir($dir);
    return 'ok.';
}

function remove_file($path)
{
    if (!file_exists($path)) return 'file not exist';
    // protect
    global $protects;
    foreach ($protects as $protect) {
        if (realpath($path) == realpath($protect)) return 'ok.';
    }
    //
    $r = unlink($path);
    if ($r === false) return 'unlink error!';
    return 'ok.';
}


function copy_dir($source, $dest)
{
    if (!file_exists($dest)) {
        $r = mkdir($dest, 0755, true);
        if ($r === false) return 'mkdir error!';
    }
    $handle = opendir($source);
    if ($handle == false) return 'opendir error!';
    while ($item = readdir($handle)) {
        if ($item == '.' || $item == '..') continue;
        $source_path = $source . '/' . $item;
        $dest_path = $dest . '/' . $item;
        if (is_file($source_path)) {
            $r = copy_file($source_path, $dest_path);
            if ($r !== 'ok.') return 'copy file error!';
        }
        if (is_dir($source_path)) {
            $r = copy_dir($source_path, $dest_path);
            if ($r !== 'ok.') return 'copy dir error!';
        }
    }
    closedir($handle);
    return 'ok.';
}

function copy_file($source, $dest)
{
    $dest_dir_path = dirname($dest);
    if (!file_exists($dest_dir_path)) {
        $r = mkdir($dest_dir_path, 0755, true);
        if ($r === false) return 'mkdir error!';
    }
    $r = copy($source, $dest);
    if ($r === false) return 'copy error!';
    return 'ok.';
}

function save_log($msg, $log_file_name) {
    $item = '[ ' . date('Y-m-d H:i:s') . ' ] ' . $msg . PHP_EOL;
    $r = file_put_contents($log_file_name, $item, FILE_APPEND);
    if ($r === false) return 'file put content error!';
    return 'ok.';
}
