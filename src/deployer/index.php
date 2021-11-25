<?php
include "config.php";

if (trim($zip_file_url) == '') {
    exit("empty zip_file_url!");
}

//获取远程文件的内容
ob_start();
readfile($zip_file_url);
$content = ob_get_contents();
ob_end_clean();

//把内容写到文件
$zip_file = @fopen("download.zip", 'a');
fwrite($zip_file, $content);
fclose($zip_file);

//解压
$zip = new ZipArchive;
$res = $zip->open('download.zip');
if ($res !== TRUE){
    exit("open zip file error!");
}
$zip->extractTo('download/');
$zip->close();

//清空目标


//复制源到目标
