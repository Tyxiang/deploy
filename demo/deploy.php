<?php
$this_file = basename(__FILE__);
$config_file = basename(__FILE__, ".php") . ".json";
$config_json = file_get_contents($config_file);
if($config_json == false){
    exit("load config error!");
}
$config = json_decode($config_json, true);
$jobs = $config["jobs"];

// main
$zip_dir = "download";
ob_end_clean();
foreach ($jobs as $key => $job){
    echo "do job " . $key;
    remove_dir($job['to_dir']);
    get_zip_to_dir($job['zip_file_url'], $zip_dir);
    copy_dir($zip_dir . '/' . $job['from_dir'], $job['to_dir']);
    remove_dir($zip_dir);
    echo " ok. <br>";
    flush(); 
	sleep(1);
}

// func
function get_zip_to_dir($zip_file_url, $zip_dir)
{
    $zip_file = basename($zip_file_url);
    $content = file_get_contents($zip_file_url);
    file_put_contents($zip_file, $content);
    $zipper = new ZipArchive;
    $zipper->open($zip_file);
    $zipper->extractTo($zip_dir);
    $zipper->close();
    unlink($zip_file);
    return true;
}

function remove_dir($dir)
{
    global $this_file, $config_file;
    $handle = opendir($dir);
    while (($item = readdir($handle)) !== false) {
        if ($item == '.' || $item == '..') continue;
        if ($item == $this_file) continue;
        if ($item == $config_file) continue;
        $path = $dir . '/' . $item;
        if (is_file($path)) unlink($path);
        if (is_dir($path)) remove_dir($path);
    }
    closedir($handle);
    if ($dir != './') {
        rmdir($dir);
    }
    return true;
}

function copy_dir($source, $dest)
{
    if (!file_exists($dest)) mkdir($dest);
    $handle = opendir($source);
    while (($item = readdir($handle)) !== false) {
        if ($item == '.' || $item == '..') continue;
        $path_source = $source . '/' . $item;
        $path_dest = $dest . '/' . $item;
        if (is_file($path_source)) copy($path_source, $path_dest);
        if (is_dir($path_source)) copy_dir($path_source, $path_dest);
    }
    closedir($handle);
    return true;
}
