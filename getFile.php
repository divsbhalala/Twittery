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
    if(!isset($_SESSION['alltweetData']) && empty($_SESSION['alltweetData']))
        {
             $_SESSION['alltweetData']=array();
        $forloop=ceil($_SESSION['totalUsersTweet']/200);
        for($i=1;$i<=$forloop;$i++)
        {
            $data = $conn->get_all_user_tweet($oauth_token,$oauth_token_secret,200,$i);
            $_SESSION['alltweetData']=array_merge($_SESSION['alltweetData'],$data);

        }
        }
        $_SESSION['tweetData']=$_SESSION['alltweetData'];

    if( $request->type == 'csv') {

        /* --------get csv from here -------------- */
        $data = $conn->getcsv($userScreenName,@$_SESSION['tweetData']);
        echo json_encode($data);
        return;
    }
    else if( $request->type == 'json') {

        /* --------get json from here -------------- */
        $data = $conn->getJson($userScreenName,@$_SESSION['tweetData']);
        echo json_encode($data);
        return;
    }
    else if( $request->type == 'xls') {

        /* --------get xls from here -------------- */
        $data = $conn->getXls($userScreenName,@$_SESSION['tweetData']);
        echo json_encode($data);
        return;
    }

}
