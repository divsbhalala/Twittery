<?php

session_start();
/*---------- Clear the current sessions--------------- */
if (isset($_REQUEST['error'])) {

   // print_r($_REQUEST['error']);exit;
    session_destroy();
    session_start();
    $_SESSION['error'] = $_REQUEST['error'];
    /*---------- redirect user with rate limit error--------------- */
    $redirect = 'index.php?error=tweetOauth';

} else if (isset($_REQUEST['unauthorized']) && $_REQUEST['unauthorized']) {

    session_destroy();
    session_start();
    /*---------- redirect user with unauthorized error--------------- */
     $_SESSION['error'] = 'Unauthorized access to this users';
    $redirect = 'index.php?error=tweetOauth';

} else {
    /*--------Remove User File  if created--------------*/
    if (count(glob("./download/" . $_SESSION['userScreen'] . ".*")) > 0) {
        array_map('unlink', glob("./download/" . $_SESSION['userScreen'] . ".*"));
    }
    session_destroy();
    $redirect = 'index.php';
}

/*--------Redirect user to the index page --------------*/
header('Location:' . $redirect);
