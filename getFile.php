<?php

include_once 'checkToken.php';

$userScreenName=$_SESSION['userScreen'];
$oauth_token=$_SESSION['access_token']['oauth_token'];
$oauth_token_secret=$_SESSION['access_token']['oauth_token_secret'];

require_once 'tweetconnection.php';
$conn = new tweetconnection();
$request = json_encode($_POST);
$request = json_decode($request);

/* --------download file from calling function -------------- */

if (isset($request->type)){
    $lastusers = @$request->users;
    include_once 'pullTweet.php';
    $data=array();
    if( $request->type == 'csv') {
        /* --------get csv from here -------------- */
        $data = $conn->getcsv($userScreenName,@$_SESSION['tweetData']);
    }
    else if( $request->type == 'json') {
        /* --------get json from here -------------- */
        $data = $conn->getJson($userScreenName,@$_SESSION['tweetData']);
    }
    else if( $request->type == 'xls') {
        /* --------get xls from here -------------- */
        $data = $conn->getXls($userScreenName,@$_SESSION['tweetData']);
    }
     echo json_encode($data);
        return;

}
