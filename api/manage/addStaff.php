<?php
require '../../config.php';
$configInfo = $conn->query("SELECT * FROM config")->fetch_assoc();
$license = $configInfo['licenseKey'];

if($_GET['key'] != $license){
    echo 'ERR';
}else{
    $checkIfThere = $conn->query("SELECT * FROM staff WHERE discord_id='".$_GET['did']."'")->num_rows;
    if($checkIfThere == 0){
        $conn->query("INSERT INTO staff (user_uid, staff_level) VALUES ('".mysqli_real_escape_string($conn, $_GET['did'])."', '".mysqli_real_escape_string($conn, $_GET['lvl'])."')");
        echo '2';
        die();
    }else{
        echo '1';
        die();
    }
}