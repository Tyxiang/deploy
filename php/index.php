<?php
$path = "新建文件夹 - 副本";  // 这里不能加 "/"，否则递归过程中对不上
echo remove_dir($path);

function remove_dir($dir)
{
	if (!file_exists($dir)) {
		return "dir not exist";
	}
	foreach (glob($dir . '/*') as $item) {
		$reserved_dir = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
		$reserved_path_php = $reserved_dir . '.php';
		$reserved_path_json = $reserved_dir . '.json';
		$reserved_path_log = $reserved_dir . '.log';
		 if (realpath($item) == $reserved_dir) {
			 continue;
		 }
		 if (realpath($item) == $reserved_path_php ) {
			 continue;
		 }
		 if (realpath($item) == $reserved_path_json) {
			 continue;
		 }
		 if (realpath($item) == $reserved_path_log) {
			 continue;
		 }
		if (is_file($item)) {
			$r = unlink($item);
			if ($r === false) {
				return "unlink error";
			}
		} else {
			$r = remove_dir($item);
			if ($r !== "ok") {
				return "remove dir error";
			}
		}
	}
	if ($dir != './') {
		$r = rmdir($dir);
		if ($r === false) {
			return "rmdir error";
		}
	}
	return "ok";
}
