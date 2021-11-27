<?php
copy_dir("a.txt", "b/c.php");

function copy_dir( $source, $dest ) {
    if ( !file_exists( $dest ) ) mkdir( $dest );
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