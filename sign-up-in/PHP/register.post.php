<?php
include_once("post.php");
include_once("autoloader.inc.php");
const DB_NAME = "localhost";
// Errors E-MAIL
const ERRORS = [
    'unique' => 'The %s already exists',
    'required' => 'The %s is required',
    'valid' => 'The %s is not a valid email address',
];
if (isset($_POST['register'])) {
    $POST = [
        "username" => htmlspecialchars(trim($_POST['username'])),
        "email" => htmlspecialchars(trim($_POST['email'])),
        "password" => htmlspecialchars(trim($_POST['password'])),
        "password2" => htmlspecialchars(trim($_POST['password2'])),
        "fname" => htmlspecialchars(trim($_POST['fname'])),
        "lname" => htmlspecialchars(trim($_POST['lname'])),
        "gender" => htmlspecialchars(trim($_POST['gender'])),
        "agree" => (bool) htmlspecialchars(trim($_POST['agree'])),
        "secure_b" => htmlspecialchars(trim($_POST['secure_b'])),
        "HTTP_USER_AGENT" => htmlspecialchars(trim($_POST['HTTP_USER_AGENT'])),
        "IP" => htmlspecialchars(trim($_POST['IP'])),
    ];
    $member = new register(
        $POST["username"],
        $POST["email"],
        $POST["password"],
        $POST["password2"],
        $POST["fname"],
        $POST["lname"],
        $POST["gender"],
        $POST["agree"],
        $POST["HTTP_USER_AGENT"],
        $POST["IP"],
        $POST["secure_b"],
    );
    $member->signUp();

    switch ($member->Messages["status"]) {
        case 'success':
            break;
        case 'error':
            break;
        default:
    }
}
exit();
?>