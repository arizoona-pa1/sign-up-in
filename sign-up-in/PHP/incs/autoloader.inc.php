<?php
$path = str_replace("\\","/",__DIR__);
$__path__ = str_replace("D:/Fuczer.com/","D:/Fuczer.com/",$path );
define('__PATH__',$__path__);
define('__DPATH__',"D:/Fuczer.com/");

include_once (__PATH__.'/function.inc.php');

function myload(string $className)
{
    $myload = __PATH__."/../classes/" . str_replace('\\', '/', $className) . ".class.php";
    
    include_once $myload;

}
spl_autoload_register('myload');

?>