<?php

session_start();
/*---------- Clear the current sessions--------------- */
$error='';
if (isset($_REQUEST['error'])) {
    $error = $_REQUEST['error'];
    /*---------- redirect user with rate limit error--------------- */
    $redirect = 'index.php?error=tweetOauth';

} else if (isset($_REQUEST['unauthorized']) && $_REQUEST['unauthorized']) {
    /*---------- redirect user with unauthorized error--------------- */
     $error = 'Unauthorized access to this users';
    $redirect = 'index.php?error=tweetOauth';

} else {
    /*--------Remove User File  if created--------------*/
    if (count(glob("./download/" . $_SESSION['userScreen'] . ".*")) > 0) {
        array_map('unlink', glob("./download/" . $_SESSION['userScreen'] . ".*"));
    }
    $redirect = 'index.php';
}

/*--------Redirect user to the index page --------------*/

    session_destroy();
    session_start();
 $_SESSION['error']=$error;
header('Location:' . $redirect);
