<?php
session_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);


/*-----------if access tokens are not available,clear session and redirect to login page-----------------------*/
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
$lastusers='';
if(isset($request->users) && !empty($request->users))
{
    $lastusers = @$request->users;
}

$filename = "download/" . $userScreenName . ".xml";
$conn->removeFile($filename);


$jsonArray = $conn->get_tweets_for_file($lastusers,$oauth_token,$oauth_token_secret);
$xml_template1 = new SimpleXMLElement("<?xml version=\"1.0\" encoding='UTF-8'?><twittery></twittery>");

$ff=arr($jsonArray, $xml_template1);
function arr($jsonArray, $xml_template1) {
    foreach ($jsonArray as $sarray => $val) {
        if (is_array($val)) {
            if (preg_match('/\A(?!XML)[a-z][\w0-9-]*/i', $sarray)) {
                arr($val, $xml_template1->addChild($sarray));
            } else {
                arr($val, $xml_template1->addChild('tweet_'.$sarray));
            }
        } else {
            
            if (preg_match('/\A(?!XML)[a-z][\w0-9-]*/i', $sarray)) {
                $xml_template1->addChild($sarray, $val);
            } else {
                $xml_template1->addChild('no_' . $sarray, $val);
            }
        }
    }
    
    return $xml_template1->asXML("download/" .$_SESSION['userScreen']. ".xml");
}

if ($ff) {
    if (file_exists($filename)) {

            $data = array('success' => true,
                'file' => $filename);
        } else {
            $data = array('success' => false,
                'file' => '');
        }
        echo json_encode($data);
                return;exit;
        }


