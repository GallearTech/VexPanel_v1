<?php
require '../../config.php';
$configInfo = $conn->query("SELECT * FROM config")->fetch_assoc();
$license = $configInfo['licenseKey'];

if($_GET['key'] != $license){
    echo 'ERR';
}else{
    $checkConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
    $siteMaintenance = $checkConfig['siteMaintenance'];
    if ($siteMaintenance == 0) {
    	echo '<sup>Online</sup>';
    	die();
    }
    if ($siteMaintenance == 1) {
    	echo '<sup>Offline</sup>';
    	die();
    }
    die();
}