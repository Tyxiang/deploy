<?php
ini_set("display_errors", "On");
date_default_timezone_set("Asia/Shanghai");
// main
$this_file_name_without_extension = basename(__FILE__, '.php'); // without extension

//// config
$config_file_name = $this_file_name_without_extension . '.json';
$config_json = file_get_contents( $config_file_name );
if ( $config_json == false ) exit( 'load config error!' );
$config = json_decode( $config_json, true );

//// jobs
$jobs = $config['jobs'];
$work_dir_name = $this_file_name_without_extension;
mkdir( $work_dir_name );
foreach ( $jobs as $key => $job ) {
    echo '<p>';
    echo '---------- job ( ' . $key . ' ) ----------';
        // download
        $url_pathinfo = pathinfo( $job['url'] );
        $download_file_name = $url_pathinfo['basename']; // with extension
        $download_file_name_without_extension = $url_pathinfo['filename']; 
        $download_file_extension = $url_pathinfo['extension']; 
        $download_file_path = $work_dir_name . '/' . $download_file_name;
        echo '<p>';
        echo 'download: ' . $job['url'] . '<br>';
        echo 'to: ' . $download_file_path . '<br>';
        download($job['url'], $download_file_path);
        echo 'ok.<br>';
        echo '</p>';
        // set source and dest
        $source = $job['from'];
        $dest = $job['to'];
        // unzip if need
        if ( $download_file_extension == 'zip' ) {
            $unzip_dir = $work_dir_name . '/' . $download_file_name_without_extension;
            // filename
            echo '<p>';
            echo 'unzip: ' . $download_file_path . '<br>';
            echo 'to: ' . $unzip_dir . '<br>';
            unzip( $download_file_path, $unzip_dir );
            echo 'ok.<br>';
            echo '</p>';
            // reset source
            $source = $work_dir_name . '/' . $job['from'];
        }
<<<<<<< HEAD
        // copy
        echo '<p>';
        // source must be exist   
        if ( !file_exists( $source ) ) {
            echo "'form' is not exist! <br>";
        }
        if ( $job['clearfirst'] ) {
            echo 'remove dest dir first ';
            remove_dir( $dest , $this_file_name_without_extension);
            echo 'ok.<br>';
        }
        echo "copy dir: " . $source . "<br>";
        echo "to: " . $dest . "<br>";
        copy_dir( $source, $dest );
        echo 'ok.<br>';
        echo '</p>';
=======
        // is exist   
        if ( !file_exists( $source ) ) {
            echo "'form' is not exist! <br>";
        }
        if ( !file_exists( $dest ) ) {
            echo "'to' is not exist! <br>"; 
        }
        // if file
        if ( is_file( $source ) && is_file( $dest ) ) {
            echo '<p>';
            if ( $job['clearfirst'] ) {
                echo 'remove dest file first ';
                unlink( $dest );
                echo 'ok.<br>';
            }
            echo "copy file: " . $source . "<br>";
            echo "to: " . $dest . "<br>";
            copy( $source, $dest );
            echo 'ok.<br>';
            echo '</p>';
        }
        // if dir
        if ( is_dir( $source ) && is_dir( $dest ) ) {
            echo '<p>';
            if ( $job['clearfirst'] ) {
                echo 'remove dest dir first ';
                remove_dir( $dest , $this_file_name_without_extension);
                echo 'ok.<br>';
            }
            echo "copy dir: " . $source . "<br>";
            echo "to: " . $dest . "<br>";
            copy_dir( $source, $dest );
            echo 'ok.<br>';
            echo '</p>';
        }
>>>>>>> 9c56d57727fe72b189f8729ef4bfbcd01e2a1d66
    echo '</p>';
}
remove_dir( $work_dir_name , $this_file_name_without_extension);

// func
function download($url, $dir){
    $content = file_get_contents( $url );
    file_put_contents( $dir, $content );
    return true;
}

function unzip( $path, $dir ) {
    $zipper = new ZipArchive;
    $r = $zipper->open( $path );
    $r = $zipper->extractTo( $dir );
    $r = $zipper->close();
    return true;
}

function remove_dir( $dir, $exc ) {
<<<<<<< HEAD
    if ( !file_exists( $dir ) ) return true;
=======
>>>>>>> 9c56d57727fe72b189f8729ef4bfbcd01e2a1d66
    $handle = opendir( $dir );
    while ( $item = readdir( $handle )) {
        if ( $item == '.' || $item == '..' ) continue;
        if ( strpos($item, $exc) === 0 ) continue;
        $path = $dir . '/' . $item;
        if ( is_file( $path ) ) unlink( $path );
        if ( is_dir( $path ) ) remove_dir( $path, $exc );
    }
    closedir( $handle );
    if ( $dir != './' ) rmdir( $dir);
    return true;
}

function copy_dir( $source, $dest ) {
<<<<<<< HEAD
    if ( !file_exists( $dest ) ) mkdir( $dest, 0755, true);
=======
    if ( !file_exists( $dest ) ) mkdir( $dest );
>>>>>>> 9c56d57727fe72b189f8729ef4bfbcd01e2a1d66
    $handle = opendir( $source );
    while  ( $item = readdir( $handle ))  {
        if ( $item == '.' || $item == '..' ) continue;
        $path_source = $source . '/' . $item;
        $path_dest = $dest . '/' . $item;
        if ( is_file( $path_source ) ) copy( $path_source, $path_dest );
        if ( is_dir( $path_source ) ) copy_dir( $path_source, $path_dest );
    }
    closedir( $handle ) ;
    return true;
}