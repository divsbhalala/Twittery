<?php

include_once 'checkToken.php';

$oauth_token=$_SESSION['access_token']['oauth_token'];
$oauth_token_secret=$_SESSION['access_token']['oauth_token_secret'];


require_once 'tweetconnection.php';
$conn = new tweetconnection();
$request = json_encode($_POST);
$request = json_decode($request);

/* --------fatch user tweet-------------- */
$tweetType = $tweetUserName = '';
if (isset($request->tweetType)) {
    $tweetType = $request->tweetType;
}
if (isset($request->tweetUserName)) {
    $tweetUserName = $request->tweetUserName;
}

if (!empty($tweetUserName) && !empty($tweetType)) {
    /* --------get followers tweet-------------- */
     if($tweetUserName=='me'){
        if(!isset($_SESSION['alltweetData']) && empty($_SESSION['alltweetData'])){
            $data = $conn->get_tweet($tweetType, $tweetUserName,$oauth_token,$oauth_token_secret);
        }
        else{
           $data=array_slice($_SESSION['alltweetData'],0,10);
        }

    }
    else{
        $data = $conn->get_tweet($tweetType, $tweetUserName,$oauth_token,$oauth_token_secret);
    }

    $_SESSION['tweetData']=$data;
    echo json_encode($data);
    return;
} else if (!empty($tweetType)) {

    /* --------get my tweet-------------- */

    if(!isset($_SESSION['MytweetData']) && empty($_SESSION['MytweetData']))
    {
        $data = $conn->get_tweet($tweetType,'',$oauth_token,$oauth_token_secret);
        $_SESSION['MytweetData']=$data;
    }
    else{
        $data=$_SESSION['MytweetData'];
    }
    echo json_encode($data);
    return;
}
