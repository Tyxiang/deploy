<?php
ini_set("display_errors", "Off");
date_default_timezone_set("Asia/Shanghai");
// main
$this_file_name_without_extension = basename(__FILE__, '.php'); // without extension

//// log prepare
$log_string = "";
$log_file_name = $this_file_name_without_extension . ".log";

//// config
$config_file_name = $this_file_name_without_extension . '.json';
$config_json = file_get_contents($config_file_name);
if ($config_json == false) {
    $message = 'load config error' . PHP_EOL;
    $log_string = $log_string . $message;
    echo '<p>';
    echo $message;
    echo '</p>';
    exit();
}
$config = json_decode($config_json, true);

//// work dir
$work_dir_name = $this_file_name_without_extension;
mkdir($work_dir_name);
//// jobs
$jobs = $config['jobs'];
foreach ($jobs as $key => $job) {
    echo '<p>';
    echo '---------- job ( ' . $key . ' ) ----------';

    echo '</p>';
}
remove_dir($work_dir_name);

//// log
echo $log_string;
file_put_contents($log_file_name, $log_string, FILE_APPEND);

// class
class Job
{
    public $work_dir_name = "";

    public $download = "";
    public $unzip = false;
    public $remove_destination = true;
    public $copy = "";
    public $to = "";

    function do() {
        $r = array();
        $download_pathinfo = pathinfo($download);
        $download_file_name = $download_pathinfo['basename']; // with extension
        $download_file_name_without_extension = $download_pathinfo['filename'];
        $download_file_name_extension = $download_pathinfo['extension'];
        $download_path = $work_dir_name . '/' . $download_file_name;
        $unzip_dir = $work_dir_name . '/' . $download_file_name_without_extension;
        $source = $work_dir_name . '/' . $copy;
        $destination = $to;
        echo '<p>';
        echo '---------- job ( ' . $key . ' ) ----------';
        // download
        echo '<p>';
        echo 'download: ' . $download . '<br>';
        $r[] = download($download, $download_path);
        echo end($r);
        echo '</p>';
        // unzip
        if ($unzip) {
            echo '<p>';
            echo 'unzip: ' . $download_file_name . '<br>';
            $r[] = unzip($download_path, $unzip_dir);
            echo end($r);
            echo '</p>';
        }
        // remove_destination
        if ($clear_destination) {
            echo '<p>';
            echo 'remove: ' . $to . '<br>';
            if (is_dir($source)) {
                $r[] = remove_dir($destination);
            }
            if (is_file($source)) {
                $r[] = remove_file($destination);
            }
            echo end($r);
            echo '</p>';
        }
        // copy
        echo "<p>";
        echo "copy: " . $source . "<br>";
        echo "to: " . $destination . "<br>";
        if (is_dir($source)) {
            $r[] = copy_dir($source, $destination);
        }
        if (is_file($source)) {
            $r[] = copy_file($source, $destination);
        }
        echo end($r);
        echo "</p>";
        return $r;
    }

    private function download($url, $dir)
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

    private function unzip($path, $dir)
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

    private function remove_dir($dir)
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

    private function copy_dir($source, $dest)
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

    private function remove_file($path)
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

    private function copy_file($source, $dest)
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
}
