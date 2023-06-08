<?php
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    exit('Invalid');
}
// $_SERVER['HTTP_REFERER']	Returns the complete URL of the current page (not reliable because not all user-agents support it)
// if($_SERVER['HTTP_REFERER'] != 'http://localhost'){
//     exit('Invalid');
// }

?>