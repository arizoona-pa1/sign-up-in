<?php
include_once("post.php");
include_once("autoloader.inc.php");

if (isset($_POST["encrypt_email"])) {

$POST = [
    "email" => htmlspecialchars(trim($_POST['email']))
];
# 1-- required E-Mail
if ($POST['email'] == "") {
    $error = sprintf(ERRORS['valid'], $POST['email']); #error email required
    // exit here
}
$query = new query(DB_NAME);
# 2-- Exists E-Mail
$is_Exists = $query->unique("users", "email", $POST['email']);

if ($is_Exists) {

    $error = sprintf(ERRORS["unique"], $POST['email']); # Error email Existed
    // exit here
} else {
    # 3-- valid E-Mail
    if (!filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = sprintf(ERRORS['valid'], $POST['email']);
        // exit here
    }
    $JSON_FIlE = new JSON("Email.json");
    $Message_mail = $JSON_FIlE->encrypt_email($POST["email"], 64, 64, (10 * 60));
    // send in E-mail$Message_mail
    // exit here
}
} else {
# happening if not request relating this issue

}