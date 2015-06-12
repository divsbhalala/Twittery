<?php
session_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);


/* --------check for access  token is exists -------------- */
if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: clearsession.php');
}

