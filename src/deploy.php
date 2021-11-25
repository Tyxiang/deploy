<?php
// job config
$jobs = array(
    array(
        "zip_file_url" => "https://github.com/Tyxiang/markdown-website/archive/refs/tags/v1.9.zip",
        "from" => "markdown-website-1.9/src",
        "to" => "./",
    ),
    array(
        "zip_file_url" => "https://github.com/Tyxiang/markdown-website/archive/refs/tags/v1.9.zip",
        "from" => "markdown-website-1.9/src",
        "to" => "./",
    ),
    array(
        "zip_file_url" => "https://github.com/Tyxiang/markdown-website/archive/refs/tags/v1.9.zip",
        "from" => "markdown-website-1.9/src",
        "to" => "./",
    )
);

// main
$zip_dir = "download";
foreach ($jobs as $key => $job){
    echo "do job " . $key;
    remove_dir($job['to']);
    get_zip_to_dir($job['zip_file_url'], $zip_dir);
    copy_dir($zip_dir . '/' . $job['from'], $job['to']);
    remove_dir($zip_dir);
    echo " ok. <br>";
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
    $handle = opendir($dir);
    while (($item = readdir($handle)) !== false) {
        if ($item == '.' || $item == '..') continue;
        if ($item == basename(__FILE__)) continue;
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
