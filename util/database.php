<?php
$docs_folder = "http://ieee.bilkent.edu.tr/grc2020/docs/submissions/";

$db_server = "localhost";
$db_user = "grc_user";
$db_password = "Monster99..";
$db_name = "grc";

$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name)
	or die('Could not be connected: ' . mysql_error());
?>
