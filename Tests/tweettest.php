<?php
/**
 * Created by PhpStorm.
 * User: spipl9
 * Date: 28/5/15
 * Time: 10:24 AM
 */

require_once 'tweetconnection.php';
class Tweettest extends PHPUnit_Framework_TestCase {

    protected $remove_file;
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
