<?php
require_once "../../../wp-config.php";

global $translate;

$codingKey        = $translate->getCodingKey();
$download_timeout = $translate->getDownloadTimetout();

// Get the remaining time for file download
$remainingTime = -1;
if (isset($_GET['time']))
    $remainingTime = time() - $translate->decode($_GET['time'], $codingKey);

// If the action and file is set and remaining download time remains
if (isset($_GET['action']) && $_GET['action'] == "email" && isset($_GET['filename']) && $remainingTime < $download_timeout) {
    $file     = $translate->decode($_GET['filename'], $codingKey);
    $filePath = $_SERVER['DOCUMENT_ROOT'] . wp_make_link_relative($file);

    // Check if the file exists before triggering the download
    if (is_file($filePath)) {

        // Clean the output buffer and disable errors reporting, to prevent file download errors
        ob_clean();
        error_reporting(0);

        // Required libraries for downloading in IE
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

        // Get the file mime type using the file extension
        switch (strtolower(substr(strrchr($filePath, '.'), 1))) {
            case "pdf":
                $mime = "application/pdf";
                break;
            case "exe":
                $mime = "application/octet-stream";
                break;
            case "zip":
                $mime = "application/zip";
                break;
            case "doc":
                $mime = "application/msword";
                break;
            case "xls":
                $mime = "application/vnd.ms-excel";
                break;
            case "ppt":
                $mime = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $mime = "image/gif";
                break;
            case "png":
                $mime = "image/png";
                break;
            case "jpe":
            case "jpeg":
            case "jpg":
                $mime = "image/jpg";
                break;
            default:
                $mime = "application/force-download";
        }

        // Prepare the response header for file transfer
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filePath));
        header('Connection: close');
        readfile($filePath);
    } else {
        echo $translate->translations['file404'] . $filePath;
    }
} else {
    echo $translate->translations['url404'];
}
exit;
