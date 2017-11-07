<?php
/*
    PHPUnit bootstrap. 
*/
spl_autoload_register(function($class) {
    if (substr($class, 0, 13) == 'drmad\\semeele') {
        $base_name = substr($class, strrpos($class, '\\') + 1);
        $file_name = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'src', $base_name . '.php']);
        if (file_exists($file_name)) {
            require $file_name;
        }
    }
});

