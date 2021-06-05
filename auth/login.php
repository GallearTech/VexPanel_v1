<?php
require '../config.php';
$configInfo = $conn->query("SELECT * FROM config")->fetch_assoc();
$license = $configInfo['licenseKey'];
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else
    $link = "http";
$link .= "://";
$link .= $_SERVER['HTTP_HOST'];
$userip = getUserIpAddr();
$time = time();
$otherInfo = $conn->query("SELECT * FROM users")->num_rows;
require '../vendor/autoload.php';
$pterodactyl = new \HCGCloud\Pterodactyl\Pterodactyl('0w3SjCSueRf7Hu8oazuw2OLPWskBsIrXx7MJHP51VytZnUib', 'https://gp.vexpanel.cf');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', $oauth_id);
define('OAUTH2_CLIENT_SECRET', $oauth_sec);

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';

session_start();

// Start the login process by sending the user to Discord's authorization page
if(get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => $oauth_red,
    'response_type' => 'code',
    'scope' => 'identify guilds email guilds.join'
  );

  // Redirect the user to Discord's authorization page
  header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}


// When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
if(get('code')) {

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => $oauth_red,
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  header('Location: ' . $_SERVER['PHP_SELF']);
}

if(session('access_token')) {
  $user = apiRequest($apiURLBase);
  //checkLicense($license, $link, $otherInfo);
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://api.vex.forcehost.net/checkLicense.php?key='.$license.'&domain='.$link.'&users='.$otherInfo,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
  ));
  
  $response = curl_exec($curl);
  
  curl_close($curl);
  echo $response;
  $invalid = base64_encode('An unknown error has happened with our API.');
  $userMax = base64_encode('This License has reached the max allowed users. Please contact support.');
  $invalidKey = base64_encode('This License key is invalid, please contact support');
  $invalidDomain = base64_encode('The allowed domain doesn\'t match this one.');
  if($response == 100){
    header("location: ../login.php?err=".$invalid);
    die();
  }elseif($response == 101){
    header("location: ../login.php?err=".$invalid);
    die();
  }elseif($response == 102){
    header("location: ../login.php?err=".$invalid);
    die();
  }elseif($response == 103){
    header("location: ../login.php?err=".$userMax);
    die();
  }elseif($response == 105){
    header("location: ../login.php?err=".$invalidKey);
    die();
  }elseif($response == 107){
    header("location: ../login.php?err=".$invalidDomain);
    die();
  }else{
  if(!$user->id || !$user->email) { 
    die("Sorry, we can't make your user if you change the scopes for the Discord oAuth2 system.");
  }

  $dbcheck = $conn->query("SELECT * FROM users WHERE discord_id='".$user->id."'");
  if($dbcheck->num_rows == 0){
    $ptero_user = genRandom(15);
    $ptero_pwd1 = genRandom(25);
    $ptero_pwd = base64_encode($ptero_pwd1);
    try {
        $ptuser = $pterodactyl->createUser([
            'email' => $user->email,
            'username' => $ptero_user,
            'password' => $ptero_pwd1,
            'language' => 'en',
            'root_admin' => false,
            'first_name' => $user->id,
            'last_name' => 'Vex-Panel'
        ]);
        $servers = apiRequest("https://discord.com/api/users/@me/guilds");
        $serversjson = json_encode($servers);
        $conn->query("INSERT INTO users (discord_usr, discord_id, user_email, first_ip, last_ip, ptero_user, ptero_pwd, ptero_uid, minutes_idle, coins, last_seen, ram, cpu, disk_space, server_slots) VALUES ('".mysqli_real_escape_string($conn, base64_encode($user->username))."', '".$user->id."', '".mysqli_real_escape_string($conn, $user->email)."', '".mysqli_real_escape_string($conn, base64_encode($userip))."', '".mysqli_real_escape_string($conn, base64_encode($userip))."', '".mysqli_real_escape_string($conn, base64_encode($ptero_user))."', '".mysqli_real_escape_string($conn, base64_encode($ptero_pwd1))."', '".$ptuser->id."', '0', '0', '".mysqli_real_escape_string($conn, $time)."', '".mysqli_real_escape_string($conn, 1000)."', '".mysqli_real_escape_string($conn, 50)."', '".mysqli_real_escape_string($conn, 10000)."', '".mysqli_real_escape_string($conn, 1)."')");
        $_SESSION['discord_user'] = $user;
        $_SESSION['loggedin'] = true;
        header("location: ../");
        die();
    } catch(\HCGCloud\Pterodactyl\Exceptions\ValidationException $e){
        print_r($e->errors());
    }
  }else{
    $_SESSION['user'] = $user;
    $_SESSION['loggedin'] = true;
    header("location: ../");
    die();
  }
  }
} else {
  header("location: ./");
}


if(get('action') == 'logout') {
  // This must to logout you, but it didn't worked(

  $params = array(
    'access_token' => $logout_token
  );

  // Redirect the user to Discord's revoke page
  header('Location: https://discord.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
  die();
}

function apiRequest($url, $post=FALSE, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  $response = curl_exec($ch);


  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if(session('access_token'))
    $headers[] = 'Authorization: Bearer ' . session('access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}

function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

function genRandom($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function checkLicense ($licenseKey, $domain, $users){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://api.vex.forcehost.net/api/checkLicense.php?key='.$licenseKey.'&&domain='.$domain.'&&users='.$users,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
$invalid = base64_encode('The license is invalid. Please contact an admin to fix this.');
if($response == '2'){
    header("location: ../login.php?err=".$invalid);
    die();
}
}


function getUserIpAddr(){
  if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      //ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      //ip pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }else{
      $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}