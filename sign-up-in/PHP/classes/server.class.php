<?php
// namespace server;
class server
{
    public static $URL;
    public static $URI;
    function __construct()
    {
        self::$URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        self::$URI = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}
// use server as nm;
// $server  = \server\server::URI;
// $server  = nm\server::URI;  // same
?>