<?php
session_start();

// load vars and functions, and connect to db

require_once("config.php");
#require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

foreach ($_GET as $key => $value) {

	// Assign $_GET array to local vars

	$$key = $value;

//	echo $key . " = <font color=\"#FF0000\">" . $value . "</font><br />";

}



foreach ($_POST as $key => $value) {

	// Assign $_POST array to local vars (for form)

	$$key = $value;



//	echo $key . " = <font color=\"#FF0000\">" . $value . "</font><br />";



// Only accept numeric chars for ZIP code {REMOVED to allow Canadian postal codes]

//if ($zip) $zip = preg_replace("/[^0-9]/", "", $zip);



}



//************** rewrite - for CURL to What Counts

$postfields = array(); 

$postfields[] = array("first", $_POST['first']); 

$postfields[] = array("last", $_POST['last']); 

$postfields[] = array("email", $_POST['email']); 

$postfields[] = array("address_1", $_POST['address_1']); 

$postfields[] = array("address_2", $_POST['address_2']); 

$postfields[] = array("city", $_POST['city']); 

$postfields[] = array("state", $_POST['state']); 

$postfields[] = array("zip", $_POST['zip']); 

$postfields[] = array("format", $_POST['format']); 

$postfields[] = array("custom_07_write_mayor", $_POST['custom_07_write_mayor']); 

$postfields[] = array("07_garbage_game_comments", $_POST['garbage_game_comments']); 

$postfields[] = array("slid_1", $_POST['slid_1']); 

$postfields[] = array("slid_2", $_POST['slid_2']); 

$postfields[] = array("slid_3", $_POST['slid_3']); 



foreach($postfields as $subarray) { 

	 list($foo, $bar) = $subarray; 

	 $bar = urlencode($bar); 

	 $postedfields[]  = "$foo=$bar"; 

}



ob_start();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?php echo $site_name; ?></title>

<style type="text/css" media="all">

<!--

body {

	background-color:#FFFFFF;

	font-family:Arial, Helvetica, sans-serif;

	font-size:0.8em;

}

/**** Forms BEGIN ****/

input, textarea, select {

	font-size: 10px;

	font-family:Arial, Helvetica, sans-serif;

	color: #000000;

	background-color: #ffffff;

	border: 1px solid #febc2c;

	padding:1px;

}

radio {

	background-color: #febc2c;

	color: #FFFFFF;

	border:none;

}

input:focus, textarea:focus, select:focus {

	background-color: #febc2c;

	border: 1px solid #FFA30D;

}

input.form-submit {

	background-color: #febc2c;

	border: 2px solid #FFA30D;

	font-weight:bold;

	padding:2px;

	margin:2x;

}

.errorfield {

	background-color: #FFCCCC;

	border: 1px solid #FF0000;

}

input.errorfield, textarea.errorfield, select.errorfield {

	font-size: 10px;

	font-family:Arial, Helvetica, sans-serif;

	color: #000000;

	background-color: #FFCCCC;

	border: 1px solid #FF0000;

}

.error_field {

	color:#FF0000;

}

/**** Forms END ****/



-->

</style>

<script language="JavaScript1.2" type="text/javascript">

<!--

function subform() {

	document.updateform.submit;

}



// Block Enter

function kH(e) {

	var pK = document.all? window.event.keyCode:e.which;

	return pK != 13;

}

document.onkeypress = kH;

if (document.layers)

document.captureEvents(Event.KEYPRESS);

// --></script>

</head>

<body document.updateform.first.focus();>

<?php

if ($function == "update") {

	// Check required fields and highlight those w/ errors

	$input_bad = 0;

	$ip_error_string = "";



	if ((($slid_1 != '') or ($slid_2 != '') or ($slid_3 != '')) and ($email == ''))   {

		$ip_error_string .= "Please provide your <b>email address</b> to subscribe to our free newsletters.<br />";

		$input_bad++;

		$red_email = 1;

	}

	if (($custom_07_write_mayor == 'yes') and ($zip == '')) {

		$ip_error_string .= "Please provide a <b>ZIP code</b>.<br />";

		$input_bad++;

		$red_zip = 1;

	}

	if ($input_bad > 0) {

		echo "<p class=\"error_field\"><b>&nbsp;&nbsp;$input_bad error";

		if ($input_bad > 1) echo "s"; // pluralize

		echo " found.  Please correct below.</b></p><p class=\"error_field\">$ip_error_string</p>";

	} else {

		// Data clean. Escape fields for MySQL and update existing user record

			$first = mysql_real_escape_string($first);

			$last = mysql_real_escape_string($last);

			$email = mysql_real_escape_string($email);

			$address_1 = mysql_real_escape_string($address_1);

			$address_2 = mysql_real_escape_string($address_2);

			$city = mysql_real_escape_string($city);

			$state = mysql_real_escape_string($state);

			$zip = mysql_real_escape_string($zip);

			$custom_07_write_mayor = mysql_real_escape_string($custom_07_write_mayor);

			$garbage_game_comments = mysql_real_escape_string($garbage_game_comments);

			$format = mysql_real_escape_string($format);

			$slid_1 = mysql_real_escape_string($slid_1);

			$slid_2 = mysql_real_escape_string($slid_2);

			$slid_3 = mysql_real_escape_string($slid_3);



		switch ($function) {

			case "update":

				// write rec to db

				$query_update_rec = "UPDATE users "

					. "SET first='$first', "

					. "last='last', "

					. "email='$email', "

					. "address_1='$address_1', "

					. "address_2='$address_2', "

					. "city='$city', "

					. "state='$state', "

					. "zip='$zip', "

					. "custom_07_write_mayor='$custom_07_write_mayor', "

					. "07_garbage_game_comments='$garbage_game_comments', "

					. "format='$format', "

					. "slid_1='$slid_1', "

					. "slid_2='$slid_2', "

					. "slid_3='$slid_3' "

					. "WHERE session_id='" . session_id() . "'";

				$result_update_rec = mysql_query($query_update_rec)

					or die("updateQuery failed : " . mysql_error());



				// CURL info to What Counts

				$urlstring = join("\n", $postedfields); 

				$urlstring = ereg_replace("\n", "&", $urlstring); 



				$ch = curl_init($curl_to); 

				curl_setopt($ch, CURLOPT_HEADER, 0); 

				curl_setopt($ch, CURLOPT_POST, 1); 

				curl_setopt($ch, CURLOPT_POSTFIELDS, $urlstring); 

				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0); 

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

				$data = curl_exec($ch); 

				curl_close($ch); 

				

				//*************Need to verify that this is working

				//echo $urlstring . "<br /><pre>DATA<br />" . $data . "</pre><br />";



				//***************** eliminate hard-code

				header('Location: ' . $site_url . "game_frame.php?rec_order=810");

				// Note: we found that the site_url variable just didn't work and specified manually. 

				# header('Location: http://www.gothamgazette.com/games/garbage_game/game_frame.php?rec_order=810');

				exit;

			break;

		}

	}

}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="updateform">

  <?php $tabindex = 1; ?>

  <input type="hidden" name="curl_to" value="whatcounts.com/bin/listctrl" />

  <input type="hidden" name="function" value="update" />

<!-- BEGIN What Counts vars -->

  <input type="hidden" name="multiadd" value="1" />

  <input type="hidden" name="cmd" value="subscribe" />

  <input type="hidden" name="key" value="slid" />

  <input type="hidden" name="custom_07_garbage_game" value="<?php echo session_id(); ?>"/>

<!-- END What Counts vars -->

<div style="width: 600px;">

      <p>Thank you for playing the Gotham Gazette Garbage Game. You can stay up to date on issues like waste management in New York City by subscribing to our free email newsletters:</p>

  <table  border="0" width="550" cellpadding="0" cellspacing="0">

    <tr>

      <td><label>

        <input type="checkbox" name="slid_1" value="" tabindex="<?php echo $tabindex++; ?>"<?php if (!empty($slid_1)) echo "checked=\"checked\""; ?>  />

        Daily Eye-Opener</label>

        <br />

        <label>

        <input type="checkbox" name="slid_3" value="" tabindex="<?php echo $tabindex++; ?>"<?php if (!empty($slid_3)) echo "checked=\"checked\""; ?> />

        Weekly Searchlight on City Hall</label>

        <br />

        <label>

        <input type="checkbox" name="slid_2" value="" tabindex="<?php echo $tabindex++; ?>"<?php if (!empty($slid_2)) echo "checked=\"checked\""; ?> />

        Monthly Environment  Newsletter</label>

        </p></td>

      <td><p>Format: <br />

          <label>

          <input type="radio" name="format" value="plain" tabindex="<?php echo $tabindex++; ?>"<?php if ($format == 'plain') echo "checked"; ?> />

          Plain-text</label>

          <br />

          <label>

          <input type="radio" name="format" value="mime" tabindex="<?php echo $tabindex++; ?>"<?php if ($format != 'plain') echo "checked"; ?> />

          HTML</label>

        </p></td>

    </tr>

  </table>

  <table border="0" cellpadding="5" cellspacing="0">

    <tr>

      <td><label>First name<br />

        <input type="text" name="first" size="25" maxlength="100" tabindex="<?php echo $tabindex++; ?>" onfocus="this.select();" value="<?php echo stripslashes($first); ?>" />

        </label></td>

      <td><label>Last name<br />

        <input type="text" name="last" size="25" maxlength="100" tabindex="<?php echo $tabindex++; ?>" onfocus="this.select();" value="<?php echo stripslashes($last); ?>" />

        </label></td>

    </tr>

  </table>

  <label<?php if ($red_email) echo " class=\"error_field\""; ?>>Email address:

  <input type="text" name="email" size="53" maxlength="100" tabindex="<?php echo $tabindex++; ?>" onfocus="this.select();" value="<?php echo stripslashes($email); ?>" />

  </label>

  <br />

  <br />

  Should we include your responses in our reporting on this issue? <br />

  <input type="radio" name="custom_07_write_mayor" value="yes" tabindex="<?php echo $tabindex++; ?>"<?php if ($custom_07_write_mayor != 'no') echo "checked=\"checked\""; ?> />

  yes

  <input type="radio" name="custom_07_write_mayor" value="no" tabindex="<?php echo $tabindex++; ?>"<?php if ($custom_07_write_mayor == 'no') echo "checked=\"checked\""; ?> />

  no

  <label<?php if ($red_zip) echo " class=\"error_field\""; ?>>What is your zip code?

  <input type="text" name="zip" size="9" maxlength="10" tabindex="<?php echo $tabindex++; ?>" onfocus="this.select();" value="<?php echo stripslashes($zip); ?>" />

  </label>

  <br />

  <br />

  <textarea name="garbage_game_comments" cols="60" rows="4" tabindex="<?php echo $tabindex++; ?>" onfocus="this.select();"><?php

if ($garbage_game_comments != 'Comments?') {

	echo stripslashes($garbage_game_comments);

} else {

	echo 'Comments?';

}

?></textarea>

  <br />

  <input type="submit" class="form-submit" tabindex="<?php echo $tabindex++; ?>" value="Submit" onclick="subform();" />

  &nbsp;&nbsp;&nbsp;&nbsp;

  <!--input type="button" class="form-submit" tabindex="<?php echo $tabindex++; ?>" value="Skip this Step" / -->

</div>

</form>

</body>

</html>

