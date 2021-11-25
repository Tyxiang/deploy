<?php
$zip = new ZipArchive;
$res = $zip->open('main.zip');
if ($res === TRUE) {
  $zip->extractTo('main/');
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}
?>