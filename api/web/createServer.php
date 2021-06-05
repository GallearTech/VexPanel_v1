<?php
session_start();
require '../../config.php';
if(isset($_SESSION['loggedin']) == true){
    $user = $_SESSION['user'];
    $userInfo = $conn->query("SELECT * FROM users WHERE discord_id='".mysqli_real_escape_string($conn, $user->id)."'")->fetch_assoc();
    $userRam = $userInfo['ram'];
    $userCPU = $userInfo['cpu'];
    $userDisk = $userInfo['disk_space'];
    $userSlots = $userInfo['server_slots'];
    $userPtero = $userInfo['ptero_uid'];
    $siteConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
    $apiKey = $siteConfig['ptero_api'];
    $apiDomain = $siteConfig['ptero_domain'];
    require '../../vendor/autoload.php';
    $pterodactyl = new \HCGCloud\Pterodactyl\Pterodactyl($apiKey, $apiDomaim);
}else{
      header("location: ../../login.php");
      die();
}


if(isset($_POST['submit'])){
$nest_id = 1;
$egg_id = $_POST['servertype'];
$egg = $pterodactyl->egg($nest_id, $egg_id); //get docker_image and startup directly from egg
try {
$server = $pterodactyl->createServer([
    "name" => $_POST['server_name'],
    "user" => $userPtero,
    "egg" => $egg_id,
    "docker_image" => $egg->dockerImage,
    "skip_scripts" => false,
    "environment" => [
        "SERVER_AUTOUPDATE" => '1',
        "BUILD_NUMBER" => 'latest',
        "SERVER_JARFILE" => 'server.jar',
        "BUNGEE_VERSION" => 'latest',
        "VANILLA_VERSION" => 'latest',
        "MINECRAFT_VERSON" => 'latest',
        "PMMP_VERSION" => 'latest',
        "NUKKIT_VERSION" => 'latest',
        "BEDROCK_VERSION" => 'latest',
        "AUTO_UPDATE" => '1',
        "USER_UPLOAD" => '1',
        "BOT_JS_FILE" => 'index.js',
        "BOT_PY_FILE" => 'index.py',
        "MAX_USERS" => "100",
        "MUMBLE_VERSION" => "1.2.19",
        "TS_VERSION" => "latest",
        "FILE_TRANSFER" => "30033",
        "BUILD_TYPE" => "recommended",
        "FORGE_VERSION" => "1.16.4-35.0.18",
        "MC_VERSION" => "latest",
        "SPONGE_VERSION" => "latest"
    ],
    "limits" => [
        "memory" => $_POST['server_ram'],
        "swap" => 0,
        "disk" => $_POST['server_hdd'],
        "io" => 500,
        "cpu" => $_POST['server_cpu']
    ],
    "feature_limits" => [
        "databases" => 0,
        "allocations" => 0,
        "backups" => 0
    ],
    "startup" => $egg->startup,
    "description" => "",
    "deploy" => [
        "locations" => [$location_id],
        "dedicated_ip" => false,
        "port_range" => []
    ],
    "start_on_completion" => true
]);
} catch(\HCGCloud\Pterodactyl\Exceptions\ValidationException $e){
    print_r($e->errors());
}
}else{
    $invalidPost = base64_encode('Sorry, you didn\'t access this page via the POST method.');
    header("location: ../../?err=".$invalidPost);
    die();
}