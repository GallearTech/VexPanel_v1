<?php
session_start();
require '../../config.php';
if(isset($_SESSION['loggedin']) == true) {
    $user = $_SESSION['user'];
    $pterodactyl_panelinfo = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc();
    $userid = $pterodactyl_panelinfo['ptero_uid'];
    $usercoins = $pterodactyl_panelinfo['coins'];
$siteConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
$apiKey = $siteConfig['ptero_api'];
$apiDomain = $siteConfig['ptero_domain'];
}else{
  header("location: ../../login.php");
}
require '../../vendor/autoload.php';
$pterodactyl = new \HCGCloud\Pterodactyl\Pterodactyl($apikey, $apiDomain);

if($_GET['id'] === null){
    header("location: ../order.php?null1");
}
if($_GET === null){
    header("location: ../order.php?null");
}

$prodid = $_GET['id'];

$prodinfo = $conn->query("SELECT * FROM products WHERE id='".mysqli_real_escape_string($conn, $prodid)."'")->fetch_assoc();
$prodprice = $prodinfo['product_price'];
$prodegg = $prodinfo['egg_id'];
$prodnest = $prodinfo['nest_id'];
$prodstock = $prodinfo['product_stock'];
$prodram = $prodinfo['product_ram'];
$prodcpu = $prodinfo['product_cpu'];
$proddisk = $prodinfo['product_disk'];
$one = 1;

if($prodstock < 0){
    header("location: ../../order.php?err=".base64_encode("Sorry, there is no more stock left on that product."));
    die();
}
if($prodstock == 0){
    header("location: ../../order.php?err=".base64_encode("Sorry, there is no more stock left on that product."));
    die();
}
if($prodprice > $usercoins){
    header("location: ../../order.php?err=".base64_encode("Sorry, you don't have enough coins to purchase this item."));
    die();
}
if ($prodprice == 0) {
    
}else{
   if($usercoins == 0){
    header("location: ../../order.php?err=".base64_encode("Sorry, you have to have more coins to purchase this item."));
    die();
} 
}

    $sql = "select node_id from srv_nodes";
    $result = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));

    $node = array();
    while($row = mysqli_fetch_assoc($result))
    {
        $node[] = $row;
    }


    $ch = curl_init($apiDomain . "/api/application/nests/".$prodnest."/eggs/" . $prodegg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $apiKey,
        "Content-Type: application/json",
        "Accept: Application/vnd.pterodactyl.v1+json"
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $result_jdecoded = json_decode($result, true);
    $docker_image = $result_jdecoded['attributes']['docker_image'];
    $startup_info = $result_jdecoded['attributes']['startup'];


    $ch = curl_init($apiDomain . "/api/application/servers");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $apiKey,
        "Content-Type: application/json",
        "Accept: Application/vnd.pterodactyl.v1+json"
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
        "name" => "$user->username's server",
        "user" => $userid,
        "nest" => $prodnest,
        "egg" => $prodegg,
        "docker_image" => $docker_image,
        "startup" => $startup_info,
        "limits" => array(
            "memory" => $prodram,
            "swap" => 0,
            "disk" => $proddisk,
            "io" => 500,
            "cpu" => $prodcpu
        ),
        "feature_limits" => array(
            "databases" => 1,
            "allocations" => 1,
            "backups" => 2
        ),
        "environment" => array(
            "DL_VERSION" => "latest",
            "SERVER_JARFILE" => "server.jar",
            "BUILD_NUMBER" => "latest",
            "BUNGEE_VERSION" => "latest",
        ),
        "deploy" => array(
            "locations" => [$node],
            "dedicated_ip" => false,
            "port_range" => []
        ),
        "start_on_completion" => false
    )));
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    $serverinfo = json_decode($result, true);
    if( $serverinfo['object'] !== "server" ) {
        header("location: ../../order.php?err=".base64_encode("An unknown error occured while creating your server. Please contact support"));
        die();
    }
if ($err) {
  header("location: ../../order.php?err=".base64_encode($err));
} else {
  $conn->query("INSERT INTO servers (server_name, server_uid, server_ram, server_cpu, server_disksp, server_alloc, owner_id) VALUES ('".mysqli_real_escape_string($conn, $serverinfo['attributes']['name'])."', '".mysqli_real_escape_string($conn, $serverinfo['attributes']['id'])."', '".mysqli_real_escape_string($conn, $prodram)."', '".mysqli_real_escape_string($conn, $prodcpu)."', '".mysqli_real_escape_string($conn, $proddisk)."', '".mysqli_real_escape_string($conn, $serverinfo['attributes']['allocation'])."', '".mysqli_real_escape_string($conn, $user->id)."', )");
    $conn->query("UPDATE users SET coins='".mysqli_real_escape_string($conn, $usercoins - $prodprice)."' WHERE discord_id='".mysqli_real_escape_string($conn, $user->id)."'");
$conn->query("UPDATE products SET product_stock='".mysqli_real_escape_string($conn, $prodstock - '1')."' WHERE id='".mysqli_real_escape_string($conn, $_GET['id'])."'");
header("location: ../../");
die();
}



/*$nest_id = $prodnest;
$egg_id = $prodegg;
$egg = $pterodactyl->egg($nest_id, $egg_id); //get docker_image and startup directly from egg
try{
$server = $pterodactyl->createServer([
    "name" => "$user->username's Server",
    "user" => $userid,
    "egg" => $egg_id,
    "docker_image" => $egg->dockerImage,
    "skip_scripts" => false,
    "environment" => [
        "SERVER_AUTOUPDATE" => '1',
        "BUILD_NUMBER" => 'latest',
        "SERVER_JARFILE" => 'server.jar',
        "BUNGEE_VERSION" => 'latest',
        "VANILLA_VERSION" => 'latest',
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
        "SPONGE_VERSION" => "latest",
    ],
    "limits" => [
        "memory" => $prodram,
        "swap" => 0,
        "disk" => $proddisk,
        "io" => 500,
        "cpu" => $prodcpu
    ],
    "feature_limits" => [
        "databases" => 0,
        "allocations" => 0,
        "backups" => 0 
    ],
    "startup" => $egg->startup,
    "description" => "",
    "deploy" => [
        "locations" => [$node],
        "dedicated_ip" => false,
        "port_range" => []
    ],
    "start_on_completion" => true
]);



$conn->query("UPDATE users SET coins='".mysqli_real_escape_string($conn, $usercoins - $prodprice)."' WHERE discord_id='".mysqli_real_escape_string($conn, $user->id)."'");
$conn->query("UPDATE products SET product_stock='".mysqli_real_escape_string($conn, $prodstock - '1')."' WHERE id='".mysqli_real_escape_string($conn, $_GET['id'])."'");
header("location: ../../");
die();
} catch(\HCGCloud\Pterodactyl\Exceptions\ValidationException $e){
    print_r($e->errors());
}*/

echo '<p><b>If your seeing this page with no other content, please talk to support!</b></p>';