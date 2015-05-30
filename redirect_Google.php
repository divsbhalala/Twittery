<?php

session_start();

ini_set("display_errors", "1");
error_reporting(E_ALL);

/*-----------if access tokens are not available,clear session and redirect to login page-----------------------*/
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: clearsession.php');
}
$userScreenName = $_SESSION['userScreen'];
$oauth_token = $_SESSION['access_token']['oauth_token'];
$oauth_token_secret = $_SESSION['access_token']['oauth_token_secret'];

/* --------Google authentication required file -------------- */
require_once 'lib/google-api-php-client/src/Google_Client.php';
require_once 'lib/google-api-php-client/src/contrib/Google_DriveService.php';

/* --------Twitter connection -------------- */
require_once 'tweetconnection.php';
$lastusers = @$_REQUEST['users'] ;
$conn = new tweetconnection();

$data = $conn->getcsv($lastusers, $userScreenName, $oauth_token, $oauth_token_secret);

if (!isset($data) && $data['success'] == 'false') {
    $_SESSION['google_upload'] = false;
    /* --------redirect to home page with error -------------- */
    header("location:home.php");
     exit;
} else {

    $filename = $data['file'];
}
/* --------Create obj of google client-------------- */
$client = new Google_Client();

/* --------set ClientId for google authentication -------------- */
$client->setClientId("849513034664-j40fjpr7p5nig9n7782572s51qg3ljk3.apps.googleusercontent.com");

/* --------set ClientSecret for google authentication-------------- */
$client->setClientSecret('WAhMbP96ujGRMHQNGGsHGMBN');

/* --------set RedirectUri for google authentication -------------- */
$client->setRedirectUri('http://twittery-rttweet.rhcloud.com/redirect_Google.php');

/* --------set Scopes for google authentication-------------- */
$client->setScopes(array('https://www.googleapis.com/auth/drive'));
$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
$client->setUseObjects(true);

/* --------Create obj of google DriveService for access drive-------------- */
$service = new Google_DriveService($client);

try {

    /* --------check google authentication is already exists-------------- */
    if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
        $tokens = json_decode($_SESSION['google_access_token']); /* Get a JSON object */
        if (!isset($_SESSION['refresh_token'])) {
            $_SESSION['refresh_token'] = $tokens->refresh_token;
        }
        /* --------check google access Token is expire?-------------- */
        if ($client->isAccessTokenExpired()) {
            /* --------refresh google access token-------------- */
            $client->refreshToken($_SESSION['refresh_token']);
            $_SESSION['google_access_token'] = $client->getAccessToken();
        }
    } else {
        /* --------check google authentication-------------- */
        $client->authenticate();
        $client->createAuthUrl();
        $_SESSION['google_access_token'] = $client->getAccessToken();
    }

    /* --------Create google drive file-------------- */
    $file = new Google_DriveFile();

    /* --------set Title for new created file-------------- */
    $file->setTitle("tweet_" . $_SESSION['userScreen']);

    /* --------sset Description for new created file-------------- */
    $file->setDescription("Tweetry Tweet Download");

    /* --------set MimeType for new created file-------------- */
    $file->setMimeType('application/vnd.google-apps.spreadsheet');
    if (isset($file->parentId) && $file->parentId != null) {

        /* --------get parent reference from google-------------- */
        $parent = new ParentReference();

        /* --------set id for file parent-------------- */
        $parent->setId($file->parentId);

        /* --------set parent to the new creted file-------------- */
        $file->setParents(array($parent));
    }

    /* --------created object fot file content-------------- */
    $data = file_get_contents($filename);

    /* --------insert the file into google drive-------------- */
    $createdFile = $service->files->insert($file, array('data' => $data, 'mimeType' => "text/csv", 'convert' => true));
    /* --------redirect to home page-------------- */
    if (isset($createdFile) && !empty($createdFile->id)) {
        $_SESSION['google_upload'] = true;
        header("location:home.php?gupload=1");
    } else {
        $_SESSION['google_upload'] = false;
        header("location:home.php?gupload=0");
    }
} catch (Exception $ex) {
    $_SESSION['google_upload'] = false;
    print_r($ex);
       /* --------redirect to home page with error-------------- */
   // header("location:home.php?gupload=0");
     exit;
}
