<?php

const ErrorText = [
    "404" => "Error : (404) file has not found .",
    "token" => [
        "wrong" => "Security code is wrong. pls try again. Careful too much try. maybe you will be blocked for 5 minutes",
        "expire" => "Your Security code time is down , resend code to get new Security code"
    ]
];
/**
 * -
 * PHP_array() return Json convert PHP array
 * -
 * find($column,$value,$type,$getValueColumn = null,$delete = false)
 * $column = column name
 * $value = value name
 * $type = ["value = return value","same = return false/true same with Default"]
 * -
 * insert(array $Data)
 * -
 * encrypt(array $Data, int $length, string $type, int $expire_t)
 * $type = ["string", "integer","both same Default"];
 * $expire_t = (2*60)
 * -
 * encrypt_verify(string $id,string $token)
 * -
 * encrypt_ExpireTime()
 * -
 */

class JSON
{
    protected $__DIR__;
    public $File;
    public $Messages = false;

    function __construct(string $file, string $dir = __PATH__ . '/../../JSON/')
    {
        $this->__DIR__ = $dir;
        $this->File = $this->__DIR__ . $file;
    }

    #

    function PHP_array()
    {
        if (file_exists($this->File)) {
            $JSON = file_get_contents($this->File);
            $PHP_array = json_decode($JSON, true);
            return $PHP_array;
        }
        return array();
    }
    /**
     * type = [same = Boolean , value = string]
     */
    function find(string $column, mixed $value, string $type, ?string $getValueColumn = null, bool $delete = false)
    {
        $PHP_array = $this->PHP_array();
        foreach ($PHP_array as $x => $x_value) {
            if ($PHP_array[$x][$column] == $value) {
                switch (strtolower($type)) {
                    case "value":
                        $Result = $PHP_array[$x][$getValueColumn];
                        break;
                    case "same":
                    default:
                        $Result = true;
                }
                if ($delete) {
                    unset($PHP_array[$x]);
                    file_put_contents($this->File, json_encode($PHP_array, JSON_PRETTY_PRINT));
                }
                return $Result;
            }
        }
        $this->Messages = ErrorText["token"]["expire"];
        return false;
    }

    function insert(array $Data)
    {
        $PHP_array = $this->PHP_array();

        $PHP_array[] = $Data;

        file_put_contents($this->File, json_encode($PHP_array, JSON_PRETTY_PRINT));
        return "file successfully appended.";
    }

    function encrypt(array $Data, int $length, string $type, int $expire_t)
    {
        $this->encrypt_ExpireTime();
        do {
            $HEX = Generate_Key(5, 5);
        } while ($this->find("ID", $HEX, "same"));


        $newData['wrap'] = $Data;
        $newData['ID'] = $HEX;
        $newData['token'] = Generate_Key(1, $length, $type);
        $newData['expire_t'] = (time() + $expire_t);
        $this->insert($newData);

        return $HEX;
    }
    function verify(string $id, string $token)
    {
        $this->encrypt_ExpireTime();
        if (!$result = $this->find("ID", $id, "value", "token")) {
            return false;
        }

        if ($token === $result) {
            return $this->find("ID", $id, "value", "wrap", true);
        } else {
            $this->Messages = ErrorText["token"]['wrong'];
            return false;
        }
    }
    
    function encrypt_email(string $email, int $length_ID, string $length_token, int $expire_t)
    {
        $this->encrypt_ExpireTime();
        do {
            $HEX = Generate_Key(1, $length_ID);
        } while ($this->find("ID", $HEX, "same"));


        $newData['email'] = $email;
        $newData['ID'] = $HEX;
        $newData['token'] = Generate_Key(1, $length_token);
        $newData['expire_t'] = (time() + $expire_t);
        $this->insert($newData);
        $url_register = "register?id=" . $newData['ID'] . "&token=" . $newData['token'];
        // send_Mail();
        // give notice user that to email verify 
        return $url_register;
    }
    function verify_email(string $id, string $token)
    {
        $this->encrypt_ExpireTime();
        if (!$result = $this->find("ID", $id, "value", "token")) {
            return false;
        }

        if ($token === $result) {
            return $this->find("ID", $id, "value", "email", true);
        } else {
            // $this->Messages = ErrorText["token"]['wrong'];
            return false;
        }
    }
 
    function encrypt_ExpireTime()
    {
        $PHP_array = $this->PHP_array();
        foreach ($PHP_array as $x => $x_value) {
            if ($x_value['expire_t'] < time()) {
                unset($PHP_array[$x]);
            }
        }
        file_put_contents($this->File, json_encode($PHP_array, JSON_PRETTY_PRINT));
    }
    function __destruct()
    {
        $this->File = null;
        $this->Messages = null;
    }
}
// $Json = new JSON("Email.json");
// $data = [
//     "email" => "fake",
//     "password" => "fake"
// ];
// echo $Json->encrypt($data, 5, "integer", (2 * 60));
// // echo $Json->encrypt_verify("","");
// echo $Json->encrypt_ExpireTime();
?>