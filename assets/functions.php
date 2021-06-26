<?php
require '../config.php';
function isStaff($userid) {
	$checkStaff = $conn->query("SELECT * FROM staff WHERE discord_id='".mysqli_real_escape_string($conn, $userid)."'")->num_rows;
	if ($checkStaff == 0) {
		return false;
	}else{
		return true;
	}
}