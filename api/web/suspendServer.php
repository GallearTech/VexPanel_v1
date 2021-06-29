<?php
session_start();
require '../../config.php';
if(isset($_SESSION['loggedin']) == true){
  $user = $_SESSION['user'];
    $siteConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
  $siteMaintenance = $siteConfig['siteMaintenance'];
  $apiKey = $siteConfig['ptero_api'];
$apiDomain = $siteConfig['ptero_domain'];
require '../../vendor/autoload.php';
$pterodactyl = new \HCGCloud\Pterodactyl\Pterodactyl($apiKey, $apiDomain);
  if ($siteMaintenance == 1) {
    header("location: ../../maintenance.php");
  }
  }else{
    header("location: ../../login.php");
    die();
  }
  $checkStaff = $conn->query("SELECT * FROM staff WHERE user_uid='".mysqli_real_escape_string($conn, $user->id)."'")->num_rows;
  if ($checkStaff == 0) {
  	header("location: ../../");
}else{
    try {
        $pterodactyl->suspendServer(14);
        header("location: ../../admin/servers.php?err=".base64_encode("The server has been suspended!"));
        die();
    } catch(\Exception $e){
      header("location: ../../admin/servers.php?err=".base64_encode($e->getMessage()));
      die();
        print_r($e->getMessage());
    }
}

