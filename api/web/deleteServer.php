<?php
$siteConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
$apiKey = $siteConfig['ptero_api'];
$apiDomain = $siteConfig['ptero_domain'];
require '../../vendor/autoload.php';
$pterodactyl = new \HCGCloud\Pterodactyl\Pterodactyl($apiKey, $apiDomain);

session_start();
include("../../config.php");
$addSlots = "1";
if( checklogin() == true ) {
$user = $_SESSION['discord_user'];
$pterodactyl_panelinfo = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc();
$pterodactyl_username = $pterodactyl_panelinfo['pterodactyl_username'];
$pterodactyl_password = $pterodactyl_panelinfo['pterodactyl_password'];
$ram = $pterodactyl_panelinfo['ram'];
$cpu = $pterodactyl_panelinfo['cpu'];
$server_slots = $pterodactyl_panelinfo['server_slots'];
$total_disk = $pterodactyl_panelinfo['disk_space'];
$readd = $conn->query("SELECT * FROM servers WHERE pterodactyl_serverid='" . mysqli_real_escape_string($conn, $_GET['id']) . "'")->fetch_assoc();
$readdRAM = $readd['ram'];
$readdCPU = $readd['cpu'];
$readdDISK = $readd['disk_space'];
} else {
  header("location: ../../login.php");
}
if( !isset($_GET['id']) || empty($_GET['id']) ) {
    header("Location: ../");
    die();
}
$checkperms = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "' AND server_uid=" . mysqli_real_escape_string($conn, $_GET['id']));
if( $checkperms->num_rows == 0 ) {
    $error = base64_encode('You don\'t have permissions to delete this server or this server doesn\'t exists.');
    header("location: ../../?err=".$error);
}
$pterodactyl->forceDeleteServer($_GET['id']);
$conn->query("DELETE FROM servers WHERE server_uid=" . mysqli_real_escape_string($conn, $_GET['id']));
header("location: ../../");

?>