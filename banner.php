<?php
// load vars and functions, and connect to db
require_once($_SERVER['DOCUMENT_ROOT']."/includes/garbage_game/config.php");

function send_default_image() {
	global $site_url, $rel_path_banners, $banner_default;
	// For whatever reason, we cannot determine which image to return, so send the default
	// x URL param appended to prevent caching
	# header('Location: ' . $site_url . $rel_path_banners . $banner_default . "?x=" . time());
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $rel_path_banners . $banner_default . "?x=" . time());
}

// Sanitize input.  $rec_id can only be numeric
$rec_id = preg_replace("/[^0-9]/", "", mysql_real_escape_string($_GET['id']));
if (($rec_id <= 0) or (empty($rec_id)) or ($rec_id == '')) {
	send_default_image();
} else {	
	$query = "SELECT rec_id, banner_image FROM users WHERE rec_id = " . $rec_id . " LIMIT 1";
	$result = mysql_query($query);
		//or die("bannerQuery failed : " . mysql_error());
	if (mysql_num_rows($result) > 0) {
		// Redirect to image file
		while ($row = mysql_fetch_object($result)) {
			if ($row->banner_image == '') {
				send_default_image();	// If, for some reason, the users.banner_image field is empty; serve default image.
				exit;
			} else {
				// x URL param appended to prevent caching
				header('Location: http://' . $_SERVER['HTTP_HOST'] . $rel_path_banners . ${$row->banner_image . '_filename'} . ".jpg?x=" . time());
			}
		}
	} else {
		// User record not found
		send_default_image();
	}
}
?>
