<?php
session_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);


/* --------check for access  token is exists -------------- */
if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: clearsession.php');
}


$userScreenName=$_SESSION['userScreen'];
$oauth_token=$_SESSION['access_token']['oauth_token'];
$oauth_token_secret=$_SESSION['access_token']['oauth_token_secret'];

require_once 'tweetconnection.php';
$conn = new tweetconnection();
$request = json_encode($_POST);
$request = json_decode($request);

/* --------download file from calling function -------------- */

if (isset($request->type)){
    if( $request->type == 'csv') {
        $lastusers = @$request->users;
         /* --------get csv from here -------------- */
        $data = $conn->getcsv($lastusers, $userScreenName,$oauth_token,$oauth_token_secret);
        echo json_encode($data);
        return;
    }
    else if( $request->type == 'json') {
        $lastusers = @$request->users;
         /* --------get json from here -------------- */
        $data = $conn->getJson($lastusers,$userScreenName,$oauth_token,$oauth_token_secret);
        echo json_encode($data);
        return;
    }
    else if( $request->type == 'xls') {
        $lastusers = @$request->users;
         /* --------get xls from here -------------- */
        $data = $conn->getXls($lastusers,$userScreenName,$oauth_token,$oauth_token_secret);
        echo json_encode($data);
        return;
    }
   
}
