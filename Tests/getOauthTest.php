<?php
session_start();
/**
 * Created by PhpStorm.
 * User: spipl9
 * Date: 28/5/15
 * Time: 11:26 AM
 */
require_once('lib/twitteroauth/TwitterOAuth.php');
require_once 'tweetconnection.php';
define('CONSUMER_KEYS', 'pXWaxgrWhLa8YZn7YVcgpbvCs');
define('CONSUMER_SECRETS', '1v8DvZprFdeFMVam957w90EwlFnOzZllgXMJrDtIcGiX6ifR7C');
define('OAUTH_ACCESS_TOKEN', '286072948-tahKrEJAyhQti4yPcHk36XetJtm1Ir4u52krNmmX');
define('OAUTH_ACCESS_TOKEN_SECRET', 'puZJzSFD36zXCPVi4LtkhwD7CMW763YfoHGN7ObOiEY2B');
class GetOauthTest extends PHPUnit_Framework_TestCase
{



    protected $data;
    protected $conn;
    protected $twitter;
    protected $remove_file;
    public function setUp()
    {

        $this->twitter = new TwitterOAuth(CONSUMER_KEYS,CONSUMER_SECRETS,OAUTH_ACCESS_TOKEN,OAUTH_ACCESS_TOKEN_SECRET);
    }

    /*--------------Twitter Oauth test-----------------------------------*/
    function testget_tweetOauth()
    {
        $this->conn = new tweetconnection();
        $this->data = $this->conn->get_tweetOauth(OAUTH_ACCESS_TOKEN,OAUTH_ACCESS_TOKEN_SECRET);
        $this->assertEquals($this->data, $this->twitter);


    }


    /*--------------Get json file and remove test---------------------------*/
    function testGetJson()
    {
        $this->conn = new tweetconnection();
        $this->data=$this->conn->getJson('','tweets',OAUTH_ACCESS_TOKEN,OAUTH_ACCESS_TOKEN_SECRET);
        $this->assertInternalType('array',$this->data);
        $this->assertEquals( $this->conn->removeFile( $this->data['file'] ), NULL );
        // $this->assertEquals($this->data, $tweets);
    }

    /*-----Remove file test------*/
    function testrrmfile()
    {
        $files = "testfile.txt";
        //-- create dummy directory with a file
        try{
            $myfile = fopen($files, "w");
            fclose($myfile);

        }catch(Exception $e)
        {
            echo "Exist";
        }

        //-- act to remove dir
        $this->remove_file = new tweetconnection();
        $this->assertEquals( $this->remove_file->removeFile( $files ), NULL );
    }



}
