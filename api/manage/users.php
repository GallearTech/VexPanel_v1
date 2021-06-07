<?php
require '../../config.php';
$configInfo = $conn->query("SELECT * FROM config")->fetch_assoc();
$license = $configInfo['licenseKey'];

if($_GET['key'] != $license){
    echo 'ERR';
}else{
    $checkNum = $conn->query("SELECT * FROM users")->num_rows;
    echo $checkNum;
    die();
}