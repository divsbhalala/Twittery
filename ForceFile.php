<?php
$ff=$_REQUEST['file'];
$filename=explode('/', $ff);
$file=$filename[count($filename)-1];
header ("Content-type: octet/stream");
header ("Content-disposition: attachment; filename=".$file.";");
readfile($ff);
exit;
