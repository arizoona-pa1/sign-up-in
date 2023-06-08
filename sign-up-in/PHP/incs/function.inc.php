<?php
/**
 * Summary of Generate_Key
 * @param int $Q_column
 * @param int $length
 * @param string|null $type
 * @return string|false
 */
function Generate_Key(int $Q_column, int $length, ?string $type = "both"): string|false
{
    switch (strtolower($type)) {
        case "string":
            $char = "qwertyuiopasdfghjklzxcvmbn";
            break;
        case "integer":
            $char = "0123456789";
            break;
        case "both":
        default:
            $char = "qwertyui01234fghjk56zxc789opasdlvmbn";

    }
    $key = "";
    for ($i = 1; $i <= $Q_column; $i++) {
        for ($b = 0; $b < $length; $b++) {
            $key .= $char[random_int(0, strlen($char)) - 1];
        }
        if ($i == $Q_column) {
            return $key;
        }
        $key .= "-";
    }
    return false;
}
// echo function_exists('Generate_Key');
function callable_func(callable $func, ?string $name, ?string $value)
{
    $func($name, $value);
}
function print_var_name($var)
{
    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
}
// function countStrposIteratorAtStart($strToFind, $value, $position = 0)
// {
//     $arr_findpos = array();
//     for ($i = 0; strlen($value); $i++) {
//         if ($value[$i] == $strToFind) {
//             array_push($arr_findpos, $i);
//         } else {
//             break;
//         }
//     }

//     return count($arr_findpos);
// }
// function backToGetStrKey($value, $findValue, $except = null)
// {
//     if ($except != null) {
//         $value = str_replace($except, "", $value);
//     }
//     $startAt = countStrposIteratorAtStart(" ", $value);
//     $positon = strpos($findValue, $value);
//     $returnValue = "";
//     for ($i = $startAt; $i <= $positon; $i++) {
//         $returnValue .= $value[$i];
//     }
//     return $returnValue;
// }
// function valid_key_yml($value)
// {
//     $validString = "/[0-9A-Za-z!@#$%&()^*-_.]+$/";
//     if (preg_match($validString, $value)) {
//         return true;
//     } else {
//         return false;
//     }
// }
// function yml_value_is_string($value){
    
// }
// function yml_value_is_array(){

// }
// function yaml_decode($filename, $nested = false)
// {
//     $hyphenSpace = "- ";
//     $semicolon = ":";
//     $angle = ">";

//     $array = array();
//     // $file = fopen($filename, "r");
//     // $contents = fread($file, filesize($filename));
//     if (!$nested) {
//         $Lines = file($filename);
//     } else {
//         $Lines = $filename;
//     }
//     for ($i_1 = 0; $i_1 < count($Lines); $i_1++) {

//         $i_2 = 1;
//         $prevLine = countStrposIteratorAtStart(" ", $Lines[$i_1]);
//         $nextLine = countStrposIteratorAtStart(" ", $Lines[$i_2]);
//         if ($prevLine == $nextLine) {
//             if (strpos()) {

//             }
//             $keyYaml = backToGetStrKey($Lines[$i_1], $semicolon, $hyphenSpace);
//             if (valid_key_yml($keyYaml)) {
//                 $array[$keyYaml] = $value;
//             }

//         } else {
//             $array[count($array)] = $value;
//         }

//     }
//     // echo $contents;
//     // fclose($file);
// }
?>