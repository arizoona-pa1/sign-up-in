<?php 
    
    class MySQL {

        private $DB_HOST = "localhost";
        private $DB_NAME;
        private $Username = "root";
        private $Password = "Khedri123";
        public $PDO;
        private $driver_options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
         );     
        
        function __construct(?string $DB_NAME = null){
            $DNS = "mysql:host=".$this->DB_HOST.";dbname=".$DB_NAME;
            try{
                $this->PDO = new PDO(
                $DNS ,
                $this->Username ,
                $this->Password ,
                $this->driver_options    
            );
                
            }catch(PDOException $e){
                die("MySQL has Error : ".$e->getMessage());
            }
        }
        public static function active()
        {
            return true;
        }
        function __destruct(){
            $this->PDO = null ;
        }
    }
 

    include_once __PATH__."/../supplements/query.supplement.php";
    include_once __PATH__."/../supplements/login.supplement.php";
    include_once __PATH__."/../supplements/register.supplement.php";
    include_once __PATH__."/../supplements/authentication.supplement.php";
    include_once __PATH__."/../supplements/user.supplement.php";

    // $member = new user(
    //     'guc12519d4-twag8q33uy-pnzl3ing9j-68liyt8ca4-5we3cq7cu6',
    //     'r0bij-pqr6t-i3adi-2n3qt-ozcn9',
    //     '1'
    // );
    // echo $member->email."\n";
    // echo $member->fname."\n";
    // echo $member->lname."\n";
    // echo $member->city."\n";
?>