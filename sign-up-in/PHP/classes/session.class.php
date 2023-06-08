<?php
ini_set('session.cookie_secure',1);
ini_set('session.cookie_httponly',1);
ini_set("session.use_trans_sid", 0);
ini_set("session.cookie_samesite", "Strict");
session_name('__ID');
class session
{
    public static function start()
    {
        session_start();
        if (!empty($_SESSION['deleted_time']) && $_SESSION['deleted_time'] < time() - (1*30*24*60*60)) {
            session_destroy();
            session_start();
        }
    }
    protected static function randhex(int $qty)
    {
        return bin2hex(random_bytes($qty));
    }
    protected static function regenerate_id()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $arr = $_SESSION;
        $newid = session_create_id(self::randhex(12) . '-' . self::randhex(12) . '-' . self::randhex(12));
        $_SESSION['deleted_time'] = time();
        session_commit();
        ini_set('session.use_strict_mode', 0);
        session_id($newid);
        session_start();
        $_SESSION = $arr;
    }
    public static function prepare(array $arr)
    {
        ini_set('session.use_strict_mode', 1);
        self::start();
        $_SESSION = $arr;
        self::regenerate_id();
    }
    public static function destory(){
        session_destroy();
    }
}

