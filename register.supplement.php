<?php

const ErrorTEXT = [
    "field" => [
        "required" => "the %s field is required",
        "valid" => "Please Enter the valid character (A-Z a-z)"
    ],
    'username' => [
        '_' => 'The dot (.), underscore (_), or hyphen (-) must not be the first or last character.',
        '_repeat' => "The dot (.), underscore (_), or hyphen (-) must not be the repeat",
        'valid' => 'Please Enter the vaild characters [A-z]-[0-9]-[._-]'
    ],
    'password' => [
        'valid' => 'Please Enter the vaild characters [A-z]-[0-9]-[!@#$%&]',
    ],
    'password2' => [
        'required' => 'Please enter the re-password again',
        'same' => 'The password does not match'
    ],
    'agree' => [
        'required' => 'You need to agree to the term of services to register'
    ],
    'gender' => [
        "valid" => "Select the Options of Gender"
    ]
];

const RegExp = [
    "string" => "/^[a-zA-Z \t][a-zA-Z \t]+$/",
    "text" => "/^[a-zA-Z \t][a-zA-Z \t]+$/",
    "username" => '/^[a-zA-Z0-9]([._-](?![._-])|[a-zA-Z0-9]){3,25}[a-zA-Z0-9]+$/',
    "password" => "/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%&]+$/"
];
const DEFAULT_VALIDATION_ERRORS = [
    'required' => 'The %s is required',
    'email' => 'The %s is not a valid email address',
    'min' => 'The %s must have at least %s characters',
    'max' => 'The %s must have at most %s characters',
    'between' => 'The %s must have between %d and %d characters',
    'same' => 'The %s does not match',
    'alphanumeric' => 'The %s should have only letters and numbers',
    'secure' => 'The %s must have between 8 and 64 characters and contain at least one number, one upper case letter, one lower case letter and one special character',
    'unique' => 'The %s already exists',
];
# ----------------------------------------------------------------------------
const fields = [
    'username' => "required | validate | unique",
    'email' => "required | validate | unique",
    'password' => "required | validate | secure",
    'password2' => "required | same",
    'fname' => "required | string",
    'lname' => "required | string",
    'gender' => "required | gender",
    'agree' => "required | validate"
];
# ----------------------------------------------------------------------------
const sucess = "Hello %s %s <br>
The email %s and username %s successfully appended. 
";
# ----------------------------------------------------------------------------

// filter_var($email, FILTER_VALIDATE_EMAIL)
// custom messages


class register extends MySQL
{
    public $username;
    public $email;
    public $password;
    public $password2;
    public $fname;
    public $lname;
    public $gender;
    public $agree;
    public $Secure_ms;
    public $Messages;
    public $USER_AGENT;
    public $IP;
    public $secure_b;
    public $id;
    public $Seasion;
    private $DB_NAME = "member";
    function __construct(
        ?string $username,
        ?string $email,
        ?string $password,
        ?string $password2,
        ?string $fname,
        ?string $lname,
        ?string $gender,
        ?bool $agree,
        ?string $USER_AGENT,
        ?string $IP,
        ?string $secure_b
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->password2 = $password2;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->gender = $gender;
        $this->agree = $agree;
        $this->USER_AGENT = $USER_AGENT;
        $this->IP = $IP;
        $this->secure_b = $secure_b;

        // $MySQL = new MySQL($this->DB_NAME);
        // $this->PDO = $MySQL->PDO;
        parent::__construct($this->DB_NAME);
    }
    protected function callable_func(
        callable $func,
        ?string $name = null,
        ?string $value = null
    ) {
        $func($name, $value);
    }
    protected function required(?string $name, ?string $value)
    {
        if ($value == "" || null || false) {
            if (isset(ErrorTEXT[$name]['required'])) {
                $message = ErrorTEXT[$name]['required'];
            } else {
                $message = sprintf(DEFAULT_VALIDATION_ERRORS["required"], $name);
            }
            $this->Messages[$name] = $message;
            return false;
        }
        return true;
    }
    protected function validate(string $name, string $value)
    {
        switch ($name) {
            case "username":
                if (!preg_match("/^[a-zA-Z0-9._-][a-zA-Z0-9._-]+$/", $value)) {
                    $this->Messages[$name] = ErrorTEXT[$name]["valid"];
                } elseif (strlen($value) < 5 || strlen($value) > 25) {
                    $this->Messages[$name] = sprintf(DEFAULT_VALIDATION_ERRORS["between"], $name, 5, 25);
                } elseif (preg_match("/^[._-]/", $value) || preg_match("/[._-]+$/", $value)) {
                    $this->Messages[$name] = ErrorTEXT[$name]["_"];
                } elseif (!preg_match(RegExp["username"], $value)) {
                    $this->Messages[$name] = ErrorTEXT[$name]["_repeat"];
                }
                break;
            case "email":
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->Messages[$name] = sprintf(DEFAULT_VALIDATION_ERRORS[$name], $value);
                }
                break;
            case "password":
                if (!preg_match("/^[a-zA-Z0-9!@#$%&][a-zA-Z0-9!@#$%&]+$/", $value)) {
                    $this->Messages[$name] = ErrorTEXT[$name]["valid"];
                }
                break;

        }
    }
    protected function string(string $name, string $value)
    {
        if (!preg_match(RegExp["string"], $value)) {
            $this->Messages[$name] = ErrorTEXT["field"]["valid"];
        }
    }
    protected function same(string $value1, string $value2)
    {
        if ($this->password !== $this->password2) {
            $this->Messages[$value1] = ErrorTEXT["password2"]["same"];
        }
    }
    protected function unique(string $name, string $value)
    {
        $user = $this->PDO->prepare("SELECT * FROM users WHERE $name = ?;");
        $user->bindValue(1, $value, PDO::PARAM_STR);
        $user->execute();
        if ($user->rowCount() != 0) {
            $this->Messages[$name] = sprintf(DEFAULT_VALIDATION_ERRORS["unique"], $value);
        }
    }
    protected function gender(string $name, ?string $value)
    {
        switch ($value) {
            case "1":
                $this->gender = "female";
                break;
            case "2":
                $this->gender = "male";
                break;
            default:
                $this->Messages['gender'] = ErrorTEXT[$name]["valid"];
                break;
        }
    }
    protected function secure(string $name, string $value)
    {
        $upper = preg_match("/[A-Z]/", $value);
        $this->Secure_ms[$name]["uppercase"] = $upper;
        $lower = preg_match("/[a-z]/", $value);
        $this->Secure_ms[$name]["lowercase"] = $lower;
        if (strlen($value) >= 8) {
            $greater = 1;
        } else {
            $greater = 0;
        }
        $this->Secure_ms[$name]["greater"] = $greater;
        $symbol = preg_match("/[!@#$%&]/", $value);
        $this->Secure_ms[$name]["symbol"] = $symbol;
        $number = preg_match("/[0-9]/", $value);
        $this->Secure_ms[$name]["number"] = $number;
        if ($greater && $lower && $upper && $number || $symbol) {
            $this->Secure_ms[$name]["condition"] = 1;
        } else {
            $this->Secure_ms[$name]["condition"] = 0;
        }
    }
    protected function config_user()
    {
        $user = $this->PDO->prepare("INSERT INTO users(`email`,`username`,`password`,`rank`)
        VALUES(?,?,?,?);");

        $this->PDO->beginTransaction();

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $user->bindValue(1, $this->email, PDO::PARAM_STR);
        $user->bindValue(2, $this->username, PDO::PARAM_STR);
        $user->bindValue(3, $this->password, PDO::PARAM_STR);
        $user->bindValue(4, 1, PDO::PARAM_INT);
        if (!$user->execute()) {
            $this->PDO->rollBack();
        }
        $this->id = $this->PDO->lastInsertId();
        $info = $this->PDO->prepare("UPDATE `personal_info` SET `firstName`=:fname, `lastName`= :lname ,`gender`=:gender WHERE `IDuser` = :id");

        $info->bindValue(':fname', $this->fname, PDO::PARAM_STR);
        $info->bindValue(':lname', $this->lname, PDO::PARAM_STR);
        $info->bindValue(':gender', $this->gender, PDO::PARAM_STR);
        $info->bindValue(':id', $this->id, PDO::PARAM_STR);
        if (!$info->execute()) {
            $this->PDO->rollBack();
        }
        $smt_select = $this->PDO->prepare("SELECT * FROM secure_b WHERE ID = ?;");

        $smt_select->bindValue(1, $this->secure_b, PDO::PARAM_STR);
        $smt_select->execute();
        if ($smt_select->rowCount() == 0) {
            $smt = $this->PDO->prepare("INSERT INTO secure_b(ID,system_info,ip,expire_t)
            VALUES(?,?,?,?);");
            do {
                $ID_HEX_SECURE_B = Generate_Key(5, 10);
                $smt_select->bindValue(1, $ID_HEX_SECURE_B, PDO::PARAM_STR);
                $smt_select->execute();
            } while ($smt_select->rowCount() != 0);
            $this->secure_b = $ID_HEX_SECURE_B;
            $smt->bindValue(1, $ID_HEX_SECURE_B, PDO::PARAM_STR);
            $smt->bindValue(2, $this->USER_AGENT, PDO::PARAM_STR);
            $smt->bindValue(3, $this->IP, PDO::PARAM_STR);
            # 1 month Time to Expire (time()+(1*30*24*60*60))
            $smt->bindValue(4, (time() + (1 * 30 * 24 * 60 * 60)), PDO::PARAM_STR);

            if (!$smt->execute()) {
                $this->PDO->rollBack();
            }
        }


        $smt = $this->PDO->prepare("INSERT INTO authentication(IDBrowser,token,IDUser,is_enable)
            VALUES(?,?,?,?);");
        $token_user = Generate_Key(5, 5);

        $smt->bindValue(1, $this->secure_b, PDO::PARAM_STR);
        $smt->bindValue(2, $token_user, PDO::PARAM_STR);
        $smt->bindValue(3, $this->id, PDO::PARAM_STR);
        $smt->bindValue(4, true, PDO::PARAM_BOOL);

        if (!$smt->execute()) {
            echo "Transaction fail";
            $this->PDO->rollback();
            return false;

        } else {
            if ($this->PDO->commit())
                $this->Seasion["token"] = $token_user;
            $this->Seasion["id"] = $this->id;
            return true;
        }
    }
    public function signUp()
    {
        foreach (fields as $nameField => $value) {

            $new = (array) $this;
            $functions = explode("|", $value);

            if (!preg_match('/[|]/i', $value)) {
                $functions[0] = $value;
            }
            foreach ($functions as $func) {

                // echo $new[$nameField];
                if (isset($this->Messages[$nameField])) {
                    continue;
                }

                $this->callable_func(
                    [$this, trim($func)],
                    $nameField,
                    $new[$nameField]
                );
            }
        }
        if (empty($this->Messages) && $this->Secure_ms["password"]["condition"] == 1) {
            $this->config_user();
            $this->Messages['success'] = sprintf(sucess, $this->fname, $this->lname, $this->email, $this->username);
        } else {
            return false;
        }
    }

}

# Cookie 
if (isset($_COOKIE['secure_b'])) {
    $secure_b = htmlspecialchars($_COOKIE['secure_b']);
} else {
    $secure_b = '';
}
$member = new register(
    "arizona",
    "pranspersian97@gmail.com",
    "Khedri123",
    "Khedri123",
    "hosien",
    "khedri",
    "2",
    true,
    $_SERVER['HTTP_USER_AGENT'],
    address::IP(),
    $secure_b
);
// $member->signUp();
// echo "<pre>";
// print_r($member->Messages);
// print_r($member->Secure_ms);
// echo "</pre>";
?>