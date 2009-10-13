<?php
session_start();

if (!isset($_GET['rec_order'])) {
	// This is a new game, so clear session variables and cookie, then destroy session
	$_SESSION = array();
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	session_destroy();
} else {
	// Suppress possible extraneous warning
	ini_set('session.bug_compat_42', 0);
	
	// Do not cache
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
	header( "Cache-Control: no-store, no-cache, must-revalidate" );
	header( "Cache-Control: post-check=0, pre-check=0", false );
	header( "Pragma: no-cache" );
}

// load vars and functions, and connect to db
require_once "config.php";
//require_once($_SERVER['DOCUMENT_ROOT']."/includes/garbage_game/config.php");

foreach ($_GET as $key => $value) {
	// Assign $_GET array to local vars
	$$key = $value;
//	echo $key . " = <font color=\"#FF0000\">" . $value . "</font><br />";
}

$ip_add = $_SERVER['REMOTE_ADDR'];

// Save impact amounts, if any

// Reduction of 9000+ rec_order necessary to accomodate 'redo' screens for first stage
if ($rec_order > 9000) {
	$session_rec_order = $rec_order - 9000;
} else {
	$session_rec_order = $rec_order;
}

//********* This is silly -- Rewrite this to iterate through an array for variable names
if (isset($tonnage_sorted)) {
	// If $_SESSION array does not exist, create it
	if (!isset($_SESSION['tonnage_sorted'])) $_SESSION['tonnage_sorted'] = array();
	// Set or replace the value for the decision made on this screen
	@$_SESSION['tonnage_sorted'][$session_rec_order] = $tonnage_sorted;
}
if (isset($tonnage_unsorted)) {
	if (!isset($_SESSION['tonnage_unsorted'])) $_SESSION['tonnage_unsorted'] = array();
	@$_SESSION['tonnage_unsorted'][$session_rec_order] = $tonnage_unsorted;
}
if (isset($tonnage_diverted)) {
	if (!isset($_SESSION['tonnage_diverted'])) $_SESSION['tonnage_diverted'] = array();
	@$_SESSION['tonnage_diverted'][$session_rec_order] = $tonnage_diverted;
}
if (isset($miles_truck_dos)) {
	if (!isset($_SESSION['miles_truck_dos'])) $_SESSION['miles_truck_dos'] = array();
	@$_SESSION['miles_truck_dos'][$session_rec_order] = $miles_truck_dos;
	if ($miles_truck_dos > 0) {
		if (!isset($_SESSION[$calculated_on_tonnage . '_miles_truck_dos'])) $_SESSION[$calculated_on_tonnage . '_miles_truck_dos'] = array();
		if (($miles_truck_dos > 0) and ($calculated_on_tonnage != '')) $_SESSION[$calculated_on_tonnage . '_miles_truck_dos'][$session_rec_order] = $miles_truck_dos;
	}
}
if (isset($miles_truck_big)) {
	if (!isset($_SESSION['miles_truck_big'])) $_SESSION['miles_truck_big'] = array();
	@$_SESSION['miles_truck_big'][$session_rec_order] = $miles_truck_big;
	if ($miles_truck_big > 0) {
		if (!isset($_SESSION[$calculated_on_tonnage . '_miles_truck_big'])) $_SESSION[$calculated_on_tonnage . '_miles_truck_big'] = array();
		if (($miles_truck_big > 0) and ($calculated_on_tonnage != '')) $_SESSION[$calculated_on_tonnage . '_miles_truck_big'][$session_rec_order] = $miles_truck_big;
	}
}
if (isset($miles_barge)) {
	if (!isset($_SESSION['miles_barge'])) $_SESSION['miles_barge'] = array();
	@$_SESSION['miles_barge'][$session_rec_order] = $miles_barge;
	if ($miles_barge > 0) {
		if (!isset($_SESSION[$calculated_on_tonnage . '_miles_barge'])) $_SESSION[$calculated_on_tonnage . '_miles_barge'] = array();
		if (($miles_barge > 0) and ($calculated_on_tonnage != '')) $_SESSION[$calculated_on_tonnage . '_miles_barge'][$session_rec_order] = $miles_barge;
	}
}
if (isset($miles_rail)) {
	if (!isset($_SESSION['miles_rail'])) $_SESSION['miles_rail'] = array();
	@$_SESSION['miles_rail'][$session_rec_order] = $miles_rail;
	if ($miles_rail > 0) {
		if (!isset($_SESSION[$calculated_on_tonnage . '_miles_rail'])) $_SESSION[$calculated_on_tonnage . '_miles_rail'] = array();
		if (($miles_rail > 0) and ($calculated_on_tonnage != '')) $_SESSION[$calculated_on_tonnage . '_miles_rail'][$session_rec_order] = $miles_rail;
	}
}

// Record the button that was clicked to get to this page
if (isset($button_clicked)) {
	// If the user is going back to change desicions, the button will contain the substring 'reset'
	// In that case, reset the $_SESSION['clicked_buttons'] array
	if (substr_count(strtolower($button_clicked), strtolower('reset')) > 0) {
		// Suppressed because this is no longer used to affect display, and user can now dip backward to redirect a single item without affecting the others
		// $_SESSION['clicked_buttons'] = array();
	} else {
		// If $_SESSION['clicked_buttons'] does not exist, create it
		if (!isset($_SESSION['clicked_buttons'])) $_SESSION['clicked_buttons'] = array();
		// If this button is not already in $_SESSION['clicked_buttons'], add it
		// This shouldn't happen in production, but conserves memory during testing
		if (!in_array($button_clicked, $_SESSION['clicked_buttons'])) $_SESSION['clicked_buttons'][] = $button_clicked;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language: en-US" />
<title><?php echo $site_name; ?></title>
<meta name="Description" content="<?php echo $HEAD_meta_description; ?>" />
<meta name="Keywords" content="<?php echo $HEAD_meta_keywords; ?>" />
<meta name="copyright" content="<?php echo $HEAD_meta_copyright; ?>" />
<meta name="resource-type" content="document" />
<meta name="robots" content="noindex,nofollow,noarchive" />
<!-- Disable MSIE image toolbar -->
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" href="garbage_game.css" type="text/css" media="all" />
<style type="text/css" media="all">
<!--
@import url("garbage_game.css");
-->
</style>
<script type="text/javascript" src="scripts/msie_png_fix.js"><!-- Fix for PNG transparency in MSIE6 --></script>
<script type="text/javascript" src="scripts/google_toolbar_form_fix.js"><!-- (Attempt to) Disable Google form field highlight --></script>
</head>
<body bgcolor="#ffffff">
<div id="dhtmltooltip"></div>
<script src="scripts/tooltip.js" type="text/javascript" language="JavaScript"></script>
<?php

// Retrieve screen layout record
$query = "SELECT * FROM " . $table_screen_text;
if (isset($rec_order)) {
	// A specific record / screen layout was requested
	// $rec_order must contain only numerals
	$rec_order = preg_replace("/[^0-9a-zA-Z]/", "", $rec_order);
	$query .= " WHERE rec_order = " . $rec_order . "";
} else {
	// Order by rec_order so that lowest numbered / first record is selected
	$query .= " ORDER BY 'rec_order' ASC";
}
$query .= " LIMIT 1";

$result = mysql_query($query)
	or debug_mail_and_die('1 Query Error:' . mysql_error() . '<br />' . $_SERVER['PHP_SELF']);

if (mysql_num_rows($result) < 1) {
	// The requested record was not found, so request the first record
	$query = "SELECT * FROM " . $table_screen_text . " ORDER BY 'rec_order' ASC";
	$result = mysql_query($query)
		or debug_mail_and_die('2 Query Error:' . mysql_error() . '<br />' . $_SERVER['PHP_SELF']);
	if (mysql_num_rows($result) < 1) {
		// There are no records in the database
		debug_mail_and_die('2 ERROR: No records were found<br />' . $_SERVER['PHP_SELF']);
	}
	
} else {
	while ($row = mysql_fetch_object($result)) {
?>
<div id="game_wholepage_<?php echo stripslashes($row->layout_template);	// Sets screen layout ?>"<?php if ($row->bg_image) echo " style=\"background-image:url('" . $rel_path_backgrounds . stripslashes($row->bg_image) . "'); background-repeat:no-repeat;\""; ?>>
  <div id="game_text_content_<?php echo stripslashes($row->layout_template);	// Sets screen layout ?>"><?php
$text_content = utf8_decode(stripslashes($row->text_content));

// ***** CALCULATIONS BEGIN *****
// Insert calculated figures into, if there appears to be a placeholder value, which are {bracketed}
if (substr_count($text_content, '{') > 0) {

// Define variables from $_SESSION data.  Errors are @suppressed to prevent throwing a warning if any of the $_SESSION arrays is not defined
	@$tonnage_sorted = array_sum($_SESSION['tonnage_sorted']);
	@$tonnage_unsorted = array_sum($_SESSION['tonnage_unsorted']);
	@$tonnage_diverted = array_sum($_SESSION['tonnage_diverted']);
/*
	echo "tonnage_sorted = " . $tonnage_sorted;
	echo "tonnage_unsorted = " . $tonnage_unsorted;
	echo "tonnage_diverted = " . $tonnage_diverted;
*/
	$tonnage_removed_from_stream = $tonnage_sorted + $tonnage_diverted;
	$_SESSION['tonnage_removed_from_stream'] = $tonnage_remove_from_stream;	// For use on forms, petitions, and other pages

//	@$tonnage_landfill = array_sum($_SESSION['tonnage_landfill']);
//	@$tonnage_recovery = array_sum($_SESSION['tonnage_recovery']);
//	@$tonnage_w2e = array_sum($_SESSION['tonnage_w2e']);

	$total_tonnage = $tonnage_unsorted + $tonnage_sorted + $tonnage_diverted;
	$_SESSION['total_tonnage'] = $total_tonnage;	// For use on forms, petitions, and other pages

	@$miles_truck_dos = array_sum($_SESSION['unsorted_miles_truck_dos']) + array_sum($_SESSION['sorted_miles_truck_dos']);
	@$miles_truck_big = array_sum($_SESSION['unsorted_miles_truck_big']) + array_sum($_SESSION['sorted_miles_truck_big']);

	@$miles_truck_dos_unsorted = array_sum($_SESSION['unsorted_miles_truck_dos']);
	@$miles_truck_big_unsorted =  array_sum($_SESSION['unsorted_miles_truck_big']);
	$miles_truck_unsorted = $miles_truck_dos_unsorted + $miles_truck_big_unsorted;

	@$miles_truck_dos_sorted = array_sum($_SESSION['sorted_miles_truck_dos']);
	@$miles_truck_big_sorted =  array_sum($_SESSION['sorted_miles_truck_big']);
	$miles_truck_sorted = $miles_truck_dos_sorted + $miles_truck_big_sorted;

	@$miles_barge_unsorted = array_sum($_SESSION['unsorted_miles_barge']);
	@$miles_barge_sorted = array_sum($_SESSION['sorted_miles_barge']);
	$miles_barge = $miles_barge_unsorted + $miles_barge_sorted;

	@$miles_rail_unsorted = array_sum($_SESSION['unsorted_miles_rail']);
	@$miles_rail_sorted = array_sum($_SESSION['sorted_miles_rail']);
	$miles_rail = $miles_rail_unsorted + $miles_rail_sorted;

	$total_mileage = $miles_truck_dos + $miles_truck_big + $miles_barge + $rail;
	$_SESSION['total_mileage'] = $total_mileage;	// For use on forms, petitions, and other pages

	// Insert formatted numbers (no decimals) into text where needed
	if (substr_count($text_content, '{tonnage_sorted}') > 0) $text_content = str_replace('{tonnage_sorted}', number_format($tonnage_sorted, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{tonnage_unsorted}') > 0) $text_content = str_replace('{tonnage_unsorted}', number_format($tonnage_unsorted, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{tonnage_diverted}') > 0) $text_content = str_replace('{tonnage_diverted}', number_format($tonnage_diverted, 0, '.', ','), $text_content);
//	if (substr_count($text_content, '{tonnage_landfill}') > 0) $text_content = str_replace('{tonnage_landfill}', number_format($tonnage_landfill, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{miles_truck_dos}') > 0) $text_content = str_replace('{miles_truck_dos}', number_format($miles_truck_dos, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{miles_truck_big}') > 0) $text_content = str_replace('{miles_truck_big}', number_format($miles_truck_big, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{miles_truck}') > 0) $text_content = str_replace('{miles_truck}', number_format($miles_truck_dos + $miles_truck_big, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{miles_barge}') > 0) $text_content = str_replace('{miles_barge}', number_format($miles_barge, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{miles_rail}') > 0) $text_content = str_replace('{miles_rail}', number_format($miles_rail, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{tonnage_removed_from_stream}') > 0) $text_content = str_replace('{tonnage_removed_from_stream}', number_format($tonnage_removed_from_stream, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{total_tonnage}') > 0) $text_content = str_replace('{total_tonnage}', number_format($total_tonnage, 0, '.', ','), $text_content);
	if (substr_count($text_content, '{total_mileage}') > 0) $text_content = str_replace('{total_mileage}', number_format($total_mileage, 0, '.', ','), $text_content);

	if (substr_count($text_content, '{total_co2}') > 0) {
		// take the total amount of garbage, divide the number of tons by 1,000, multiply by the number of miles and then multiply that by the appropriate variable from config.php, depending on the transportation
	
		$truck_co2 = ($total_tonnage / 1000) * ($miles_truck_dos + $miles_truck_big) * $truck_co2_per_mile_per_1k_tons;
		$rail_co2 = ($total_tonnage / 1000) * $miles_rail * $rail_co2_per_mile_per_1k_tons;
		$barge_co2 = ($total_tonnage / 1000) * $miles_barge * $barge_co2_per_mile_per_1k_tons;
	
		$total_co2 = $truck_co2 + $rail_co2 + $barge_co2;
		$_SESSION['total_co2'] = $total_co2;	// For use on forms and petitions and other pages
	
		$text_content = str_replace('{total_co2}', number_format($total_co2, 0, '.', ','), $text_content);
	}

	if (substr_count($text_content, '{total_cost}') > 0) {
		$cost_landfill = $tonnage_unsorted * $cost_ton_landfill;
		$cost_sorted = $tonnage_sorted * $cost_ton_sorted;
//		$cost_recovery = $tonnage_recovery * $cost_ton_recovery;
//		$cost_w2e = $tonnage_w2e * $cost_ton_w2e;
		
//		$total_cost = $cost_landfill + $cost_sorted + $cost_recovery + $cost_w2e;
		$total_cost = $cost_landfill + $cost_sorted;

		$text_content = str_replace('{total_cost}', number_format($total_cost, 0, '.', ','), $text_content);

		// Calculate miles waste travels
		$truck_dos_travel = (($miles_truck_dos_unsorted * $tonnage_unsorted) / $truck_dos_capacity) + (($miles_truck_dos_sorted * $tonnage_sorted) / $truck_dos_capacity);
		$truck_big_travel = (($miles_truck_big_unsorted * $tonnage_unsorted) / $truck_big_capacity) + (($miles_truck_big_sorted * $tonnage_sorted) / $truck_big_capacity);
		$barge_travel     = (($miles_barge_unsorted * $tonnage_unsorted) / $barge_capacity) + (($miles_barge_sorted * $tonnage_sorted) / $barge_capacity);
		$rail_travel      = (($miles_rail_unsorted * $tonnage_unsorted) / $rail_capacity) + (($miles_rail_sorted * $tonnage_sorted) / $rail_capacity);
		//echo '<font color=#FF0000>rail calculation = (( ' . $miles_rail_unsorted . ' * ' . $tonnage_unsorted . ' ) / ' . $rail_capacity . ' ) + (( ' . $miles_rail_sorted . ' * ' . $tonnage_sorted . ' ) / ' . $rail_capacity . ' )</font>';
		
		$text_content = str_replace('{truck_dos_travel}',  number_format($truck_dos_travel, 0, '.', ','), $text_content);
		$text_content = str_replace('{truck_big_travel}',  number_format($truck_big_travel, 0, '.', ','), $text_content);
		$text_content = str_replace('{barge_travel}', number_format($barge_travel, 0, '.', ','), $text_content);
		$text_content = str_replace('{rail_travel}', number_format($rail_travel, 0, '.', ','), $text_content);
		
		//Amanda, trying to generate a figure for the total distance traveled all around ...
		$big_total_mileage = ($truck_dos_travel + $truck_big_travel + $barge_travel + $rail_travel);
		$text_content = str_replace('{big_total_mileage}', number_format($big_total_mileage, 0, '.', ','), $text_content);
		$_SESSION['big_total_mileage'] = $big_total_mileage;	// For use on forms, petitions, and other pages

// ***** CALCULATIONS END *****

		// Set final $_SESSION vars or use on forms and petitions and other pages
		$_SESSION['total_cost'] = $total_cost;
		$_SESSION['total_tonnage_sorted'] = $tonnage_sorted;
		$_SESSION['total_tonnage_unsorted'] = $tonnage_unsorted;
		$_SESSION['total_tonnage_diverted'] = $tonnage_diverted;
		$_SESSION['tonnage_removed_from_stream'] = $tonnage_removed_from_stream;
		$_SESSION['total_tonnage'] = $total_tonnage;
		$_SESSION['miles_truck_dos'] = $miles_truck_dos;
		$_SESSION['miles_truck_big'] = $miles_truck_big;
		$_SESSION['miles_truck_sorted'] = $miles_truck_sorted;
		$_SESSION['miles_truck_unsorted'] = $miles_truck_unsorted;
		$_SESSION['miles_barge'] = $miles_barge;
		$_SESSION['miles_rail'] = $miles_rail;

		// Game complete.  
		
		// Assign banner image
		// Count items sorted
		foreach ($_SESSION['tonnage_sorted'] as $key => $val)  {
			if ($val > 0) $count_sorted++;
		}
		// Count items in trash
		foreach ($_SESSION['tonnage_unsorted'] as $key => $val)  {
			if ($val > 0) $count_unsorted++;
		}
		// Count items diverted
		foreach ($_SESSION['tonnage_diverted'] as $key => $val)  {
			if ($val > 0) $count_diverted++;
		}

		// Determine which banner image applies to the user
		if ($count_sorted > $count_unsorted) {
			// User sorted more items than they put in trash; give green banner
			$banner_image = $banner_green_filename;
		} elseif ($count_unsorted > $count_sorted) {
			// User put more items in trash than they sorted; give landfill banner
			$banner_image = $banner_landfill_filename;
		} else {
			// Equal number of items sorted and in trash; number of items diverted is the tie breaker
			if ($count_diverted > 0) {
				// They diverted something; give them green banner
				$banner_image = $banner_green_filename;
			} else {
				// They diverted nothing; give them landfill banner
				$banner_image = $banner_landfill_filename;
			}
		}

		//Record this user's session.
		$query_user = "INSERT INTO users (session_id, banner_image, ip_add) VALUES ('" . session_id()  . "', '$banner_image', '$ip_add')";
		$result_user = mysql_query($query_user)
			or die("userQuery failed : " . mysql_error());
		$rec_id = mysql_insert_id();
		$_SESSION['user_id'] = $rec_id;

		// Record this user's answers.
		foreach ($_SESSION as $key => $val)  {
		   if (is_array($_SESSION[$key])) {
		   		// This is an array, so save each value it contains
				foreach ($_SESSION[$key] as $subkey => $subval) {
//					echo "&nbsp;&nbsp;&nbsp;" . $subkey . " = " . $subval . "<br />";
					$query_answers = "INSERT INTO user_choices (session_id, array_name, button_number, value)";
					$query_answers .= " VALUES ('" . session_id() . "', '" . $key . "', '" . $subkey . "', '" . $subval . "')";
					$result_answers = mysql_query($query_answers)
						or die("answerQuery failed : " . mysql_error());
				}
			} else {
				// This is a string, not an array, so save it as a single value
//			   echo $key . " = " . $val . "<br />";
				$query_answers = "INSERT INTO user_choices (session_id, array_name, button_number, value)";
				$query_answers .= " VALUES ('" . session_id()  . "', '" . $key . "', '" . $key . "', '" . $val . "')";
				$result_answers = mysql_query($query_answers)
					or die("answerQuery failed : " . mysql_error());
			}
		}
	}
	
	// Insert banner text if requested
	if (substr_count($text_content, '{banner_text}') > 0) {
		$banner_text = "<div style=\"align:center;background:URL(http://www.gothamgazette.com/games/garbage_game/banner.php?id=" . $_SESSION['user_id'] . ");font-family: sans-serif;color:#000000; border: 1px solid #fff; width:330px;height:100px\">I played <a href=\"http://www.gothamgazette.com/games/garbage.php\" target=\"new\">The Gotham Gazette Garbage Game</a> and sent ";
		$banner_text .= number_format($_SESSION['total_tonnage'], 0, '.', ',') . " tons of refuse across ";
		$banner_text .= number_format($_SESSION['big_total_mileage'], 0, '.', ',') . " miles.</div>";
		$text_content = str_replace('{banner_text}', $banner_text, $text_content);
	}
	if (substr_count($text_content, '{banner_code}') > 0) {
		$banner_code = "<p style=\"font-weight:normal; font-size:10px; background-color:#ffffff; padding:5px;\">";
		$banner_code .= "&lt;!--GOTHAM GAZETTE GARBAGE GAME (begin)--&gt;<br />";
		$banner_code .= "&lt;div style=\"background:URL(http://www.gothamgazette.com/games/garbage_game/banner.php?id=" . $_SESSION['user_id'] . ");font-family: sans-serif;color:#000000;width:330px;height:100px\"&gt;";
		$banner_code .= "I played &lt;a href=&quot;http://www.gothamgazette.com/games/garbage.php&quot; target=&quot;new&quot;&gt;The Gotham Gazette Garbage Game&lt;/a&gt; and sent " . number_format($_SESSION['total_tonnage'], 0, '.', ',') . " tons of refuse across ".number_format($_SESSION['big_total_mileage'], 0, '.', ',') ." miles.";
		$banner_code .= "&lt;/div&gt;";
		$banner_code .= "<br />&lt;!--GOTHAM GAZETTE GARBAGE GAME (end)--&gt;";
		$banner_code .= "</p>";

		$text_content = str_replace('{banner_code}', $banner_code, $text_content);
	}
}

// Write out content with all {replacements} that were triggered
echo $text_content;
?></div>
  <div id="game_central_image_<?php echo stripslashes($row->layout_template);	// Sets screen layout ?>">
    <?php
	$rec_order = $row->rec_order;	// necessary if default first record was selected, otherwise doesn't hurt anything

	// Display emblem 'central' image if defined
	$query_central_image = "SELECT image_name, file_name FROM " . $table_images_central . " WHERE rec_order = " . $rec_order . " LIMIT 1";
	$result_central_image = mysql_query($query_central_image)
		or debug_mail_and_die('3 Query Error:' . mysql_error() . '<br />' . $_SERVER['PHP_SELF']);
	if (mysql_num_rows($result_central_image) > 0) {
		// Display image
		while ($row_central_image = mysql_fetch_object($result_central_image)) {
			$alt_text = ucfirst(str_replace('_', ' ', stripslashes($row_central_image->image_name)));
			echo "<image src=\"" . $rel_path_central . stripslashes($row_central_image->file_name) . "\" id=\"" . stripslashes($row_central_image->image_name) . "\" alt=\"" . $alt_text . "\" title=\"" . $alt_text . "\">";
		}
		mysql_free_result($result_central_image);
	}
?>
  </div>
  <div id="game_buttons_<?php echo stripslashes($row->layout_template);	// Sets screen layout ?>"><?php
// Display buttons and links, if defined

//	$query_buttons = "SELECT image_name, file_name, short_text, rollover_text, go_to, landfill_tonnage, miles_truck_dos, miles_truck_big, miles_barge, display_order FROM " . $table_images_buttons . " WHERE rec_order = " . $rec_order . " ORDER BY 'display_order' ASC";
	$query_buttons = "SELECT * FROM " . $table_images_buttons . " WHERE rec_order = " . $rec_order . " ORDER BY 'display_order' ASC";
	$result_buttons = mysql_query($query_buttons)
		or debug_mail_and_die('4 Query Error:' . mysql_error() . '<br />' . $_SERVER['PHP_SELF']);
	if (mysql_num_rows($result_buttons) > 0) {
		// Display each button with link
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n<tr valign=\"top\">\n";
		$td_count = 0;
		while ($row_buttons = mysql_fetch_object($result_buttons)) {
			// Build URL variable string
			$url_params = "?rec_order=" . stripslashes($row_buttons->go_to);
			if ($row->change_choices != "y") {
				// This is not a summary page, so include variables to track impact amounts $_SESSION vars
				$url_params .= "&tonnage_sorted=" . $row_buttons->tonnage_sorted;
				$url_params .= "&tonnage_unsorted=" . $row_buttons->tonnage_unsorted;
				$url_params .= "&tonnage_diverted=" . $row_buttons->tonnage_diverted;
	//			$url_params .= "&tonnage_landfill=" . $row_buttons->tonnage_landfill;
	//			$url_params .= "&tonnage_recovery=" . $row_buttons->tonnage_recovery;
	//			$url_params .= "&tonnage_w2e=" . $row_buttons->tonnage_w2e;
				if ($row_buttons->calculated_on_tonnage != '') $url_params .= "&calculated_on_tonnage=" . $row_buttons->calculated_on_tonnage;
				$url_params .= "&miles_truck_dos=" . $row_buttons->miles_truck_dos;
				$url_params .= "&miles_truck_big=" . $row_buttons->miles_truck_big;
				$url_params .= "&miles_barge=" . $row_buttons->miles_barge;
				$url_params .= "&miles_rail=" . $row_buttons->miles_rail;
			}

			// Only show this button if it has not already been clicked
			if ($row_buttons->image_name == 'show_form') {
				$next_button_html = "<a href=\"subform.php\" class=\"button_link\"><image src=\"" . $rel_path_buttons . stripslashes($row_buttons->file_name) . "\" id=\"" . utf8_decode(stripslashes($row_buttons->short_text)) . "\" alt=\"\" title=\"Next Screen\" border=\"0\"></a>";
			} elseif (substr_count($row_buttons->image_name, 'next') > 0) {
				// Special positioning for Next button
				$next_button_html = "<a href=\"" . $_SERVER['PHP_SELF'] . $url_params . "\" class=\"button_link\"><image src=\"" . $rel_path_buttons . stripslashes($row_buttons->file_name) . "\" id=\"" . utf8_decode(stripslashes($row_buttons->short_text)) . "\" alt=\"Next Screen\" title=\"\" border=\"0\"></a>";
			} elseif (($hide_button_after_clicking == 0) or !isset($_SESSION['clicked_buttons']) or (!in_array($row_buttons->rec_order . $row_buttons->image_name, $_SESSION['clicked_buttons']))) {
				
				if ($row->change_choices != "y") {
					// This is not a summary page, so append button ID in URL params to that it will be logged in SESSION array on next page
					$url_params .= "&button_clicked=" . $row_buttons->rec_order . $row_buttons->image_name;
				}

				if ($row->change_choices == "y") {
					// This is a summary page, so pull images from summary directory, not button directory.  Also, generate code for second table row.
					$button_image_file_path = $rel_path_summaries;

					// Append to second row of buttons for summary pages
		
					// Calculate which button to display based on user's most recent previous choice.
					if (!empty($_SESSION['clicked_buttons'])) {
						// Determine last action taken with this item from last relevant value in session clicked_buttons array.
						foreach ($_SESSION['clicked_buttons'] as $summary_key => $summary_val) {
							if (substr_count(stripslashes($summary_val), stripslashes($row_buttons->image_name)) > 0) {
								// This session var applies to this image; strip prepended numerals and assign to image file name
								$image_file = ltrim(stripslashes($summary_val), '01234567890') . ".png";
							}
						}
					}
		
					$summaries_tr2_html .= "<td><a href=\"" . $_SERVER['PHP_SELF'] . "?rec_order=" . stripslashes($row_buttons->go_to) . "\" class=\"button_link\"";
					if (!empty($row_buttons->rollover_text)) echo " onmouseover=\"tooltip('<span class=black_text>" . str_replace('', '\u0027', str_replace('\'', '\u0027', utf8_decode(stripslashes($row_buttons->short_text)) . "</span><br /><br />" . utf8_decode($row_buttons->rollover_text))) . "', 300);\" onmouseout=\"hidetooltip();\"";
					$summaries_tr2_html .= ">";
					$summaries_tr2_html .= "<image src=\"" . $button_image_file_path . $image_file . "\" id=\"" . utf8_decode(stripslashes($row_buttons->short_text)) . "\" alt=\"\" title=\"\" border=\"0\">";
					$summaries_tr2_html .= "</a></td>\n";

				} else {
					// Not a summary page, so use regular button direcoty.
					$button_image_file_path = $rel_path_buttons;
				}

				echo "<td align=\"center\" width=\"" . round($button_table_width / mysql_num_rows($result_buttons)) . "\">";

				echo "<a href=\"" . $_SERVER['PHP_SELF'] . $url_params . "\" class=\"button_link\"";
				if  (!empty($row_buttons->rollover_text)) echo " onmouseover=\"tooltip('<span class=black_text>" . str_replace('', '\u0027', str_replace('\'', '\u0027', utf8_decode(stripslashes($row_buttons->short_text)) . "</span><br /><br />" . utf8_decode($row_buttons->rollover_text))) . "', 300);\" onmouseout=\"hidetooltip();\"";
				echo ">";
				echo "<image src=\"" . $button_image_file_path . $row_buttons->file_name . "\" id=\"" . utf8_decode(stripslashes($row_buttons->short_text)) . "\" alt=\"\" title=\"\" border=\"0\">";
				echo "</a>";
				if ($row_buttons->short_text) {
					echo "<br /><a href=\"" . $_SERVER['PHP_SELF'] . $url_params . "\" class=\"button_link\">" . utf8_decode(stripslashes($row_buttons->short_text)) . "</a>";
				}
				echo "<br /><img src=\"resources/structure/spacer.gif\" width=\"" . round($button_table_width / mysql_num_rows($result_buttons)) . "\" height=\"1\" alt=\"\">";
				echo "</td>\n";
			}
		}
		echo "</tr>\n";

		// If this is a summary page, a second table row was created above.  Print it.
		if ($row->change_choices == "y") {
			if (!empty($summaries_tr2_html)) echo "<tr valign=\"top\">" . $summaries_tr2_html . "</tr>\n";
		}

		echo "</table>\n";
		mysql_free_result($result_buttons);
	}
?>
  </div>
  <?php
  		// 'Next' buttons are not displayed in the table above, but instead are placed at the bottom right corner in their own <div>
  		if ($next_button_html) echo "<div id=\"next_button_" . stripslashes($row->layout_template) . "\">" . $next_button_html . "</div>\n";
	}
}
?>
</div>
<?php 
if ($debug_on_screen == 'y') {
	echo '<pre><p><strong>DEBUG INFO:</strong></p>';
	echo $query_user . "<br />";
	echo $query_answers . "<br />";
	print_r($_SESSION);
	echo '</pre>';
}
?>
</body>
</html>
