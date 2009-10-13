<?php
session_start();
setcookie('testcookie', true);
// load variables
?>
<html>
<head>
	<title>Gotham Gazette - The Garbage Game</title>

	<style type="text/css" media="all">
	<!--
	#game_iframe {
		background-color:#FFFFFF;
		border:none;
		width:630px;
		height:400px;
	}
	-->
	</style>

</head>
<body>

<!-- ********* BEGIN GAME ********* -->

<iframe id="game_iframe" src="game_frame.php" scrolling="no"></iframe>

<?php
// Check for cookie set by HTML wrapper.  If not there, show alert.
if ($_COOKIE['testcookie'] != true) echo "<p style=\"color:#FFFFFF; background-color:#FF8888; padding:3px;\">The Garbage Game uses cookies to track your score. You're welcome to play without a score but the conclusion won't be any fun. Please enable cookies and reload this page.</p>";
?>
<!-- ********* END GAME  ********* -->

	</body>
</html>
