<?php
$ff=$_REQUEST['file'];
$filename=explode('/', $ff);
$file=$filename[count($filename)-1];
header ("Content-type: octet/stream");
header ("Content-disposition: attachment; filename=".$file.";");
header("Content-Length: ".filesize($ff));
readfile($_REQUEST['file']);
exit;
?>
