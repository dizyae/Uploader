<?php
    
    namespace Library;

    require(__DIR__."/../vendor/autoload.php");
    require(__DIR__."/../src/autoload.php");

    function move_uploaded_file($filename, $destination)
    {
        if(is_dir($filename))
        {
            return false;
        }
        return copy($filename, $destination);
    }