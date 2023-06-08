<?php
include_once("post.php");
include_once("incs/autoloader.inc.php");

if(isset($_POST['login_auth_email'])) {

    $POST = [
        "secure_id" => htmlspecialchars(trim($_POST['secure_id'])),
        "secure_token" => htmlspecialchars(trim($_POST['secure_token']))
    ];

    $login = new security();
    $verification = $login->verify_code(
        $POST['secure_id'],
        $POST['secure_token']
    );
    if ($verification === true) {
        # success message + seasion
        echo json_encode($login->Seasion);
    } else {
        # call Error message
        echo json_encode($verification);
        #or
        echo json_encode($login->Messages);
    }
}
exit();
?>