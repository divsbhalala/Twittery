<?php
session_start();
require_once('lib/twitteroauth/TwitterOAuth.php');
require_once('tweetconnection.php');


/* -----------if access tokens are not available,clear session and redirect to login page----------------------- */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: clearsession.php');
}

$conn = new tweetconnection();
/* -----------get user access tokens from the session----------------------- */
$access_token = $_SESSION['access_token'];

/* -----------create a TwitterOauth object with tokens----------------------- */
$twitteroauth = $conn->get_tweetOauth($access_token['oauth_token'], $access_token['oauth_token_secret']);
/* -----------get the current user's info----------------------- */

if(!isset($_SESSION['user_info']) && empty($_SESSION['user_info'])){
    $user_info = $twitteroauth->get('account/verify_credentials');
    $_SESSION['user_info']=$user_info;
}
else{
    $user_info=$_SESSION['user_info'];
}

/* -----------check twitter Oauth ----------------------- */
if (isset($user_info->errors)) {
    header('location:clearsession.php?error=' . $user_info->errors[0]->message);
}

/* -----------set user screen name to session for file creation----------------------- */
$_SESSION['userScreen'] = $user_info->screen_name;
$_SESSION['totalUsersTweet']=$user_info->statuses_count;
$_SESSION['totalUsersFollowers']=$user_info->followers_count;

//
/* -----------get the followers list----------------------- */

if(!isset($_SESSION['user_friends'])&&empty($_SESSION['user_friends']))
{
     $friend_list='';
    if(count($user_info->followers_count)>0)
    {
     $friend_list = $twitteroauth->get("https://api.twitter.com/1.1/followers/list.json?cursor=-1&screen_name=" . $user_info->screen_name . "&skip_status=true&include_user_entities=false&count=".$_SESSION['totalUsersFollowers']);   
    }
    
    $_SESSION['user_friends']=$friend_list;
}
else{
    $friend_list=$_SESSION['user_friends'];
}
?>

<!DOCTYPE html>
<html class="full" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Twittery apps for display your latest tweets">
        <meta name="author" content="Twittery apps">

        <title>Twittery</title>

        <!-- Bootstrap Core CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="assets/css/full.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/flexslider.css" type="text/css" media="screen"/>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    
        <![endif]-->

    </head>

    <body>

        <nav class="navbar navbar-tweet " role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <img src="assets/img/logo3.png" class=" img-responsive" alt="Twittery">
                </div>
            </div>
        </nav>

        <div class="wraper" style="position: relative">
            <div class="row">
                <div class="col-md-12">

                    <?php
                    if (isset($user_info->profile_banner_url)) {
                        ?>
                        <img src="<?php echo $user_info->profile_banner_url . '/1500x500' ?>" class="wall">
                        <?php
                    } else {
                        ?>
                        <div class="wall wall-color"></div>
                        <?php
                    }
                    ?>


                    <div class="profile col-sm-2 pad-0 col-xs-12">
                        <img src="<?php echo str_replace("_normal", "", $user_info->profile_image_url); ?>"
                             class="img-responsive">

                        <div class="text-center profile-name ">
                            <span><?php echo $user_info->name ?></span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-9 col-sm-9 col-xs-12 margin-top-10 pull-right">

                    <div class="col-md-2 col-sm-3 col-xs-4 text-center counts ">
                        <span><tweetCount><?php echo @$user_info->statuses_count; ?></tweetCount><br>  Tweets</span>
                    </div>
                    <div class="col-md-2 col-sm-3 col-xs-4 text-center counts ">
                        <span><?php echo @$user_info->friends_count; ?><br>  Following</span>
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-4 text-center counts ">
                        <span><followers><?php echo @$user_info->followers_count; ?></followers><br>  Followers</span>
                    </div>


                </div>


            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="col-md-12  col-sm-12 col-xs-12 pad-0 ">
                        <div class="col-md-12 pad-0 ">
                            <div class="form-group col-md-3 col-sm-3 col-xs-6">

                                <button type="button" class="btn btn-info btn-block" id="home">Home</button>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-6">
                                <button type="button" class="btn btn-info btn-block" id="mytweet">My Tweet</button>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-6">
                                <a href="clearsession.php" class="btn btn-info btn-block">Logout</a>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-6">
                                <button type="button" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="true" id="downloadtweets"> Download As
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="downloadtweets">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="download"
                                                               fileType="csv">CSV</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="download"
                                                               fileType="xls">Excel</a>
                                    </li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="download"
                                                               fileType="xml">XML</a>
                                    </li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="download"
                                                               fileType="json">JSON</a>
                                    </li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="download"
                                                               fileType="googleSheet">Google Spredsheet</a></li>


                                </ul>
                            </div>


                        </div>


                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 pad-0 tweet-list">
                        <?php
                        if (isset($_SESSION['google_upload']) && isset($_GET['gupload'])) {
                            if ($_SESSION['google_upload']) {
                                ?>
                                <div class="alert googleAlert  alert-success text-center">
                                    <span class="msg">
                                        File successfully added to your google drive
                                    </span>
                                </div>
                                <?php
                            } else if($_SESSION['google_upload']==FALSE) {
                                ?>
                                <div class="alert googleAlert  alert-warning text-center">
                                    <span class="msg">
                                     Opps!  Error in file file add to google drive
                                    </span>
                                </div> 
                                <?php
                            }
                          $_SESSION['google_upload']=NULL;  
                        }
                        ?>
                        <div class="alert operation-alert alert-warning text-center">
                            <span class="msg">

                            </span>
                        </div>
                        <div class="flexslider">
                            <div class=" newslide">


                            </div>
                        </div>


                    </div>

                    <?php
                    /* -----------Disply search followers if followers is exists----------------------- */
                    if (isset($friend_list->users) && count($friend_list->users) > 0) {
                        ?>
                        <div id="hacker-list">
                            <div class="col-md-12 col-sm-12 form-group margin-top-10 pad-0" >
                                <input type="text" class="form-control search" placeholder="Search Followers">

                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 pad-0">
                                <ul class="list -unstyled pad-0 list " style="overflow: hidden">
                                    <?php
                                    if ($friend_list->users) {
                                        foreach ($friend_list->users as $friends) {
                                            ?>
                                            <li class="col-md-2 col-sm-2 col-xs-6 tweet-user bg-tweet-color text-center">
                                                <a href="javascript:void(0)" id="<?php echo $friends->screen_name ?>">
                                                    <img src="<?php echo $friends->profile_image_url ?>" alt="profile image"
                                                         class="img-circle">
                                                    <br>
                                                    <span class="user-title name"><?php echo $friends->name ?></span>
                                                    <span class="user-desc hidden">@<span
                                                            class="screen-name"><?php echo $friends->screen_name ?></span></span>

                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php
                    }
                    ?>



                </div>

            </div>
        </div>

        <!------------Model for download progress-------------------------------->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="z-index: 100;"><span
                                aria-hidden="true">&times;</span></button>
                        <b class="text-center text-primary download-msg">Please wait while preparing your file</b>

                        <div style="position: relative;" id="download_loader">
                            <div id="loader" style="opacity: 1;margin:0;left:30%"></div>
                        </div>
                        <a class="btn btn-primary " href="" id="downloadNow" download target="_blank">Download</a>

                    </div>

                </div>
            </div>

        </div>

        <!------------ End model for download progress-------------------------------->
        <script src="assets/js/jquery.js"></script>
        <script type="text/javascript" src="assets/js/moment.js"></script>
        <script src="assets/js/list.js"></script>
        <script defer src="assets/js/jquery.flexslider.js"></script>
        <script  src="assets/js/modernizr.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>


        <script defer src="assets/js/script.js"></script>
        <script>
            $users = '<?php echo @$_SESSION['userScreen'] ?>';
            
        </script>
    </body>

    <!-- jQuery -->

</html>
