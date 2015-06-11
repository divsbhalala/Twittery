<?php
//disable warnings . delete the code beofre moving to production
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
session_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);
require_once('lib/twitteroauth/TwitterOAuth.php');
include('config.php');

try {
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
// Requesting authentication tokens, the parameter is the URL we will be redirected to
    $request_token = $twitteroauth->getRequestToken(OAUTH_CALLBACK);

// Saving them into the session
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    switch ($twitteroauth->http_code) {
        case 200:
            //Build authorize URL and redirect user to Twitter.
            $url = $twitteroauth->getAuthorizeURL($_SESSION['oauth_token']);
            header('Location: ' . $url);
            break;
        default:
            //Error
            echo "some error occured";
    }
} catch (Exception $exc) {
    header('location:index.php');
}
