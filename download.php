<?php
require_once "../../../wp-config.php";

function decode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}
global $translate;
$codingKey = $translate->getCodingKey();
$download_timeout = $translate->getDownloadTimetout();
if(isset($_GET['action']) && $_GET['action']=="email" && isset($_GET['time']) && (time()-decode($_GET['time'],$codingKey))<$download_timeout) {
	$file = decode($_GET['filename'],$codingKey);
  $filePath = $_SERVER['DOCUMENT_ROOT'].wp_make_link_relative($file);

  // make sure it's a file before doing anything!
  if(is_file($filePath)) {

    // Disable errors
    error_reporting(0);
    // Clean everything in output
    ob_clean();

  	/*
  		Do any processing you'd like here:
  		1.  Increment a counter
  		2.  Do something with the DB
  		3.  Check user permissions
  		4.  Anything you want!
  	*/

  	// required for IE
  	if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

  	// get the file mime type using the file extension
  	switch(strtolower(substr(strrchr($filePath, '.'), 1))) {
      case "pdf": $mime="application/pdf"; break;
      case "exe": $mime="application/octet-stream"; break;
      case "zip": $mime="application/zip"; break;
      case "doc": $mime="application/msword"; break;
      case "xls": $mime="application/vnd.ms-excel"; break;
      case "ppt": $mime="application/vnd.ms-powerpoint"; break;
      case "gif": $mime="image/gif"; break;
      case "png": $mime="image/png"; break;
      case "jpe": case "jpeg":
      case "jpg": $mime="image/jpg"; break;
      default: $mime="application/force-download";
  	}

  	header('Pragma: public'); 	// required
  	header('Expires: 0');		// no cache
  	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  	header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($filePath)).' GMT');
  	header('Cache-Control: private',false);
  	header('Content-Type: '.$mime);
  	header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
  	header('Content-Transfer-Encoding: binary');
  	header('Content-Length: '.filesize($filePath));	// provide file size
  	header('Connection: close');
  	readfile($filePath);		// push it out
  } else {
    echo 'File doesnt exist:'.$filePath;
  }
} else {
	echo "Dieses Link ist nicht mehr gültig!";
}
exit;
