<?php
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
