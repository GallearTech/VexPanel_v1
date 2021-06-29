<?php
$siteName = '';
$siteVersion = "v1.3.2 BETA";

function latestVersion(){
	$latestVersion = file_get_contents("https://raw.githubusercontent.com/GallearTech/VexPanel_v1/main/latestVersion.txt");
	echo $latestVersion;
}

$oauth_id = '';
$oauth_sec = '';
$oauth_red = '';

$serverName = "";
$dBUsername = "";
$dBPassword = "";
$dBName = "";
$conn = mysqli_connect($serverName, $dBUsername, $dBPassword, $dBName);

if(!$conn){
	header("location: /errors/500.html");
}