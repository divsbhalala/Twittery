<?php

//session_start();
/* ----------Twitter authentication required file---------- */
require_once('lib/twitteroauth/TwitterOAuth.php');

/* ----------For excell---------- */
require_once('lib/php-export-data.class.php');


require_once('config.php');

class tweetconnection {

    public $access_token = array();
    public $twitteroauth;
    public $downloadPath;

    public function get_tweetOauth($oauth_token = null, $oauth_token_secret = null) {
        if ($oauth_token == null || $oauth_token_secret == null) {
            header('Location: clearsession.php');
        }
        $this->twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
        return $this->twitteroauth;
    }

    public function get_tweet($tweetType = '', $tweetUserName = '', $oauth_token = null, $oauth_token_secret = null, $count = 10)
    {

        if ($oauth_token == null || $oauth_token_secret == null) {
            header('Location: clearsession.php');
        }

        $this->get_tweetOauth($oauth_token, $oauth_token_secret);

        $user_info = $this->twitteroauth->get('account/verify_credentials');
        /* ----------Redirect user if twitter oauth Rate limit error---------- */
        $this->userInfoError($user_info);
        /* ----------get the latest 10 tweets of the current user from his timline---------- */
        $tweets=array();
        if ($tweetType == "home") {
            $tweets = $this->twitteroauth->get("https://api.twitter.com/1.1/statuses/home_timeline.json?screen_name=" . $user_info->screen_name . "&count=" . $count . "&contributor_details=true");
        } else if ($tweetType == "followers") {
            if ($tweetUserName == "me") {
                $username = $user_info->screen_name;
            } else {
                $username = $tweetUserName;
            }
            $tweets = $this->twitteroauth->get("https://api.twitter.com/1.1/statuses/user_timeline.json?include_entities=true&screen_name=" . $username . "&count=" . $count);
            $this->tweetError($tweets);
        }
        return $tweets;
    }

 public function get_all_user_tweet($oauth_token = null, $oauth_token_secret = null, $count = 10,$page=1)
    {

        if ($oauth_token == null || $oauth_token_secret == null) {
            header('Location: clearsession.php');
        }

        $this->get_tweetOauth($oauth_token, $oauth_token_secret);

        $user_info = $this->twitteroauth->get('account/verify_credentials');
        /* ----------Redirect user if twitter oauth Rate limit error---------- */
        $this->userInfoError($user_info);
       
        /* ----------get the latest 10 tweets of the current user from his timline---------- */
       
            $username = $user_info->screen_name;
            $tweets = $this->twitteroauth->get("https://api.twitter.com/1.1/statuses/user_timeline.json?include_entities=true&screen_name=" . $username . "&count=" . $count.'&page='.$page);
           $this->tweetError($tweets);
        

        return $tweets;
    }
 public  function get_tweets_for_file($tweets) {

        $jsonArray = array();
        foreach ($tweets as $line) {
            /* ----------generate array lines from the tweets---------- */
            
            if (isset($line->retweeted_status)) {
                $username = $line->retweeted_status->user->name;
                $screenname = $line->retweeted_status->user->screen_name;
                $retweeted = $line->user->name;
                $profileImg = $line->retweeted_status->user->profile_image_url;
                $text = $line->retweeted_status->text;
                $fav_cnt = $line->retweeted_status->favorite_count;
                $created = $line->retweeted_status->created_at;
                $idstr = $line->retweeted_status->id_str;
            } else {
                $username = $line->user->name;
                $screenname = $line->user->screen_name;
                $retweeted = '';
                $profileImg = $line->user->profile_image_url;
                $text = $line->text;
                $fav_cnt = $line->favorite_count;
                $created = $line->created_at;
                $idstr = $line->id_str;
            }
            $allmedia = array();
            if (isset($line->extended_entities)) {
                foreach ($line->extended_entities->media as $media) {
                    array_push($allmedia, $media->media_url);
                }
            }

            $retweet_count = $line->retweet_count;
            $myjson = array(
                'id_str' => $idstr,
                'created_at' => $created,
                'text' => $text,
                'retweet_by' => $retweeted,
                'name' => $username,
                'screen_name' => $screenname,
                'profile_image_url' => $profileImg,
                'favorite_count' => $fav_cnt,
                'retweet_count' => $retweet_count,
                'media_url' => $allmedia
            );
            array_push($jsonArray, $myjson);
        }

        return $jsonArray;
    }

    /* ----------genrate csv file---------- */

    public function getcsv( $forFileName = 'tweets',$tweets) {
        $filename = "download/" . $forFileName . ".csv";
        /* ----------remove already created file---------- */
        $this->removeFile($filename);

        $delimiter = ",";
        /* ----------open raw memory as file so no temp files needed, you might run out of memory though---------- */
        $f = fopen($filename, 'w');

        fputcsv($f, array("id_str", "created_at", "text", "retweet_by", "name", "screen_name", "profile_image_url", "favorite_count", "retweet_count", "media_url"), $delimiter);

        $jsonArray = $this->get_tweets_for_file($tweets);
        /* ----------loop over the input array---------- */
        foreach ($jsonArray as $singleArray) {
            fputcsv($f, array($singleArray['id_str'] , $singleArray['created_at'], $singleArray['text'], $singleArray['retweet_by'], $singleArray['name'], $singleArray['screen_name'], $singleArray['profile_image_url'], $singleArray['favorite_count'], $singleArray['retweet_count'],implode(' , ', @$singleArray['media_url'])), $delimiter);
        }


        fseek($f, 0);
        $data=$this->isFileCreated($filename);

        /* ----------return  data with file name or iscreated---------- */
        return $data;
    }

    /* ----------genrate json file---------- */

    public function getJson( $forFileName = 'tweets',$tweets) {
        $jsonArray = $this->get_tweets_for_file($tweets);
        $filename = "download/" . $forFileName . ".json";
        /* ----------remove already created file--------- */
        $this->removeFile($filename);
        $fp = fopen($filename, 'w');
        fwrite($fp, json_encode($jsonArray, JSON_PRETTY_PRINT));
        fclose($fp);
        /* ----------Check whether file is created--------- */
       $data=$this->isFileCreated($filename);
        return $data;
    }

    /* ----------genrate xls file--------- */

    public function getXls( $forFileName = 'tweets',$tweets) {

        $jsonArray = $this->get_tweets_for_file($tweets);

        $filename = "download/" . $forFileName . ".xls";

        /* ----------remove already created file--------- */
        $this->removeFile($filename);

        $exporter = new ExportDataExcel('file', $filename);
        $exporter->initialize();

        $exporter->addRow(array("id_str", "created_at", "text", "retweet_by", "name", "screen_name", "profile_image_url", "favorite_count", "retweet_count", "media_url"));
        foreach ($jsonArray as $singleArray) {
            $exporter->addRow(array($singleArray['id_str'], $singleArray['created_at'], $singleArray['text'], $singleArray['retweet_by'], $singleArray['name'], $singleArray['screen_name'], $singleArray['profile_image_url'], $singleArray['favorite_count'], $singleArray['retweet_count'], implode(' , ', @$singleArray['media_url'])));
        }

        $exporter->finalize();
        /* ----------Check whether file is created--------- */
       $data=$this->isFileCreated($filename);
        return $data;
    }

    /* ----------Remove files--------- */

    public function removeFile($filename) {

        /* ----------file is already exists if then remove--------- */
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
     public function tweetError($tweets) {

        /* ---------check for tweet errors--------- */
       if (isset($tweets->error) && $tweets->error == "Not authorized") {
        header('location:clearsession.php?unauthorized=' . true);
        }
    }
    public function userInfoError($user_info) {

        /* ---------check for user information errors--------- */
      if (isset($user_info->errors)) {
        header('location:clearsession.php?error=' . $user_info->errors[0]->message);
        }
    }
     public function isFileCreated($filename) {
        chmod($filename, 0777);
        /* ---------check for is file created return with name and status isfile created --------- */
      if (file_exists($filename)) {
            $data = array('success' => true,
                        'file' => $filename);
        } else {
            $data = array('success' => false,
            'file' => '');
        }
        return $data;
    }

}
