<?php
include_once("post.php");
include_once("incs/autoloader.inc.php");

$status = [
    'success' => json_encode(array('status' => 'success', 'message' => 'Login successful')),
    'error' => json_encode(array('status' => 'error', 'message' => 'Invalid username or password')),
    'invalidToken' => json_encode(array('status' => 'error', 'message' => 'Invalid token'))

];
if (isset($_POST['login'])) {
    // echo "yes you're here login.php";
    $user = $_POST['username'] ?? $_POST['email'];
    $POST = [
        "username" => htmlspecialchars(trim($user)),
        "password" => htmlspecialchars(trim($_POST['password'] ?? '')),
        "secure_b" => htmlspecialchars(trim($_POST['secure_b'] ?? '')),
        "HTTP_USER_AGENT" => htmlspecialchars(trim($_POST['HTTP_USER_AGENT'] ?? '')),
        "IP" => htmlspecialchars(trim($_POST['IP'] ?? '')),
    ];

    $login = new login(
        $POST['username'],
        $POST['password'],
        $POST["HTTP_USER_AGENT"],
        $POST["IP"],
        $POST["secure_b"]
    );
    $result = $login->connect();
    switch ($result) {
        case true:
            echo json_encode($login->Seasion);

            echo $status['success'];
            #or
            #echo json_encode($login->Messages);

            break;
        case false:
            echo $status['error'];
            #or
            #echo json_encode($login->Messages);
            break;
        default:
            #echo json_encode(array('token' => $result));
            echo json_encode($login->Messages);
    }
}
exit();
?>