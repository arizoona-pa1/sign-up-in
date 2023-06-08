<?php
// include_once "../function.inc.php";
// include_once "../autoloader.inc.php";
const ErrorText_login = [
    "Login" => "There was an error with your E-Mail/Username and Password combination. Please try again.",
    "MySQL" => "An error occurred in MySQl to inserting data, please try again later. until resolve this issue"
];
const success_login = "";
class login extends MySQL
{
    /**
     *  username        type [] string
     *  password        type [] string
     *  Seasion["token"];
     *  Seasion["id"];
     */
    public $Username;
    protected $Password;
    public $USER_AGENT;
    protected $IP;
    public $Messages = false;
    public $Seasion;
    public $secure_b;
    private $JSON_NAME = "Login.json";
    private $DB_NAME = "member";
    function __construct(
        ?string $Username,
        ?string $Password,
        string $USER_AGENT,
        string $IP,
        ?string $secure_b
    ) {
        $this->Username = $Username;
        $this->Password = $Password;
        $this->USER_AGENT = $USER_AGENT;
        $this->IP = $IP;
        $this->secure_b = $secure_b;

        parent::__construct($this->DB_NAME);
    }
    private function verify_Password(mixed $Mix): bool
    {
        return password_verify($this->Password, $Mix->password);
    }

    private function Config_user($ID)
    {

        $smt = $this->PDO->prepare("SELECT * FROM authentication WHERE IDBrowser = :secure_b AND IDuser = :ID");
        $smt->bindValue(":secure_b", $this->secure_b, PDO::PARAM_STR);
        $smt->bindValue(":ID", $ID, PDO::PARAM_STR);
       
        if ($smt->execute()) {
            if ($smt->rowCount() != 0) {
                $this->Seasion["token"] = $smt->fetch()->token;
                $this->Seasion["id"] = $ID;
                return true;
            } else {
                # connect with Security Code 
                $Security = new JSON($this->JSON_NAME);
                $data["id"] = $ID;
                $data["HTTP_USER_AGENT"] = $this->USER_AGENT;
                $data["IP"] = $this->IP;
                $data["secure_b"] = $this->secure_b;
                return $Security->encrypt($data, 5, "both", (2 * 60));
            }
        }

    }
    function connect()
    {
        $search = [
            'username',
            'email'
        ];
        # Email 
        # User
        foreach ($search as $value) {
            $smt = $this->PDO->prepare("SELECT * FROM users WHERE $value=?");
            $smt->bindValue(1, $this->Username, PDO::PARAM_STR);

            if ($smt->execute()) {
                if ($smt->rowCount() != 0) {
                    $user = $smt->fetch();
                    if ($this->verify_Password($user)) {
                        return $this->Config_user((int) $user->ID);
                    }
                }
            }
        }

        $this->Messages = ErrorText_login["Login"];
        return false;
    }

    function __destruct()
    {
        $this->Username = null;
        $this->Password = null;
        $this->Messages = null;
        $this->Seasion = null;
        $this->secure_b = null;
        $this->PDO = null;
    }
}
class security extends MySQL
{
    private $DB_NAME = "fuczer";
    private $secure_b;
    private $JSON_NAME = "Login.json";
    public $Messages;
    public $Seasion;
    function __construct()
    {
        parent::__construct($this->DB_NAME);
    }
    function resend_code(string $id)
    {

    }
    function verify_code(string $id, string $token)
    {
        $Security = new JSON($this->JSON_NAME);
        $GET_E = $Security->verify($id, $token);
        if (is_array($GET_E)) {
            $query = new query($this->DB_NAME);
            $this->PDO->beginTransaction();
            if (!$query->find_bool("secure_b", "ID", $GET_E['secure_b'])) {
                do {
                    $ID_HEX_SECURE_B = Generate_Key(5, 10);
                } while ($query->find_bool("secure_b", "ID", $ID_HEX_SECURE_B));

                $smt = $this->PDO->prepare("INSERT INTO secure_b(ID,system_info,ip,expire_t)
                    VALUES(?,?,?,?);");

                $this->secure_b = $ID_HEX_SECURE_B;
                $smt->bindValue(1, $this->secure_b, PDO::PARAM_STR);
                $smt->bindValue(2, $GET_E["HTTP_USER_AGENT"], PDO::PARAM_STR);
                $smt->bindValue(3, $GET_E["IP"], PDO::PARAM_STR);
                # 1 month Time to Expire (time()+(1*30*24*60*60))
                $smt->bindValue(4, (time() + (1 * 30 * 24 * 60 * 60)), PDO::PARAM_STR);

                if (!$smt->execute()) {
                    $this->PDO->rollBack();
                    return ErrorText_login['MySQL'];
                }
            }
            $smt = $this->PDO->prepare("INSERT INTO authentication(ID_browser,token,ID_user,is_enable)
                VALUES(?,?,?,?);");
            $token_user = Generate_Key(5, 5);
            $smt->bindValue(1, $this->secure_b ?? $GET_E["secure_b"], PDO::PARAM_STR);
            $smt->bindValue(2, $token_user, PDO::PARAM_STR);
            $smt->bindValue(3, $GET_E["id"], PDO::PARAM_STR);
            $smt->bindValue(4, true, PDO::PARAM_BOOL);

            if (!$smt->execute()) {
                $this->Messages = ErrorText_login["MySQL"];
                $this->PDO->rollback();
                return false;
            } else {
                if ($this->PDO->commit()) {
                    $this->Messages['success'] = success_login;
                    $this->Seasion["token"] = $token_user;
                    $this->Seasion["id"] = $GET_E["id"];
                    return true;
                }
            }
        } else {
            return $Security->Messages;
        }
    }
}
// $login = new login("mehran@hotmail.com", "Ws09170811556", $_SERVER["HTTP_USER_AGENT"], "192.1.132.2", "das");
// $result = $login->connect();
// if ($result === true) {
//     print_r($login->Seasion);
// } else {
//     # set in html with id  
//     if (isset($login->Messages)) {
//         print_r($login->Messages);
//     }
//     if (isset($result)) {
//         print($result);
//     }
// }
// --------------------------------------
// $Login = new security();
// $GET_V = $Login->verify_code(
//     "ld7dp-lgcli-6nmng-tof8o-mg2sn",
//     "eg39i"
// );
// if ($GET_V === true) {
//     echo "<pre>";
//     print_r($Login->Messages);
//     print_r($Login->Seasion);
//     echo "</pre>";
// } else {
//     echo $GET_V;
// }