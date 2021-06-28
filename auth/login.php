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
$siteConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
$apiKey = $siteConfig['ptero_api'];
$apiDomain = $siteConfig['ptero_domain'];
//require './vendor/autoload.php';
//$pterodactyl = new \HCGCloud\Pterodactyl\Pterodactyl($apiKey, $apiDomain);
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
  if(!$user->id || !$user->email) { 
    die("Sorry, we can't make your user if you change the scopes for the Discord oAuth2 system.");
  }
  if(isProxy($userip) == true) {
    $proxyErr = base64_encode("Your IP address seems to be a VPN or Proxy. Please contact support if you think this is a mistake!");
        header("location: ../login.php?err=".$proxyErr);
        die();
      }
  $checkDatabase = $conn->query("SELECT * FROM users WHERE discord_id='".mysqli_real_escape_string($conn, $user->id)."'");

  if ($checkDatabase->num_rows == 0) {
        $ptero_pwd = genRandom(25);
    $ptero_user = genRandom(17);
    $curl = curl_init($apiDomain."/api/application/users");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      "Authorization: Bearer ".$apiKey,
      "Content-Type: application/json",
      "Accept: Application/vnd.pterodactyl.v1+json"
    ));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
      "username" => $ptero_user,
      "email" => $user->email,
      "first_name" => "A",
      "last_name" => "User",
      "password" => $ptero_pwd,
    )));
    $userRes = curl_exec($curl);
        $err = curl_error($curl);
    curl_close($curl);
        if ($err) {
          $errCode = base64_encode($err);
          header("location: ../login.php?err=".$errCode);
          die();
          }else{
            $ptuser = json_decode($userRes, true);
        $conn->query("INSERT INTO users (discord_usr, discord_id, user_email, first_ip, last_ip, ptero_user, ptero_pwd, ptero_uid, minutes_idle, coins, last_seen, ram, cpu, disk_space, server_slots) VALUES ('".mysqli_real_escape_string($conn, base64_encode($user->username))."', '".$user->id."', '".mysqli_real_escape_string($conn, $user->email)."', '".mysqli_real_escape_string($conn, base64_encode($userip))."', '".mysqli_real_escape_string($conn, base64_encode($userip))."', '".mysqli_real_escape_string($conn, base64_encode($ptero_user))."', '".mysqli_real_escape_string($conn, base64_encode($ptero_pwd))."', '".$ptuser['attributes']['id']."', '0', '0', '".mysqli_real_escape_string($conn, $time)."', '".mysqli_real_escape_string($conn, 1000)."', '".mysqli_real_escape_string($conn, 50)."', '".mysqli_real_escape_string($conn, 10000)."', '".mysqli_real_escape_string($conn, 1)."')");
        $conn->query("INSERT INTO user_sessions (session_userid, session_id, session_ip, session_device, session_status) VALUES ('".mysqli_real_escape_string($conn, $user->id)."', '".mysqli_real_escape_string($conn, session_id())."', '".mysqli_real_escape_string($conn, $userip)."', '".mysqli_real_escape_string($conn, 'Err #JKwi23JF')."', 1)");
          $_SESSION['user'] = $user;
          $_SESSION['loggedin'] = true;
          header("location: ../");
          }
  }else{
    $conn->query("INSERT INTO user_sessions (session_userid, session_id, session_ip, session_device, session_status) VALUES ('".mysqli_real_escape_string($conn, $user->id)."', '".mysqli_real_escape_string($conn, session_id())."', '".mysqli_real_escape_string($conn, $userip)."', '".mysqli_real_escape_string($conn, 'Err #JKwi23JF')."', 1)");
          $_SESSION['user'] = $user;
          $_SESSION['loggedin'] = true;
          header("location: ../");
  }
}else{
  header("location: ../login.php");
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
function isProxy($ip) {
  $d = file_get_contents("https://db-ip.com/" . $ip);
  $hosting = false;
  $proxy = false;
  if(strpos($d, 'Hosting') !== false) {
    $hosting = true;
  }
  if(strpos($d, 'This IP address is used by a proxy') !== false) {
    $proxy = true;
  }
  if( $hosting == true || $proxy == true ) {
    return true;
  } else {
    return false;
  }
}
?>
