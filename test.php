<?php
session_start();
mysql_connect("localhost", "admin", "ping") or die(mysql_error());
mysql_select_db("jar_project") or die(mysql_error());

if (isset($_SESSION['loggedin'])){
	$query = "SELECT * FROM users where username = '". $_SESSION['username'] ."'"; 
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result) or die(mysql_error());
	print $row['password'];
}
?>