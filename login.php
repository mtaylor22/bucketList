<?php
session_start();
require 'functions.php';
	connect();
if (isset($_POST['submit'])){
	check_login($_POST['username'], $_POST['password']);
}
$user = get_logged_user();
if ($user)
	print 'hi, your password is ' . $user['password'];

?>

<form method="POST" action="login.php">
	<table style="text-align:center;">
		<tr><td>Username</td><td><input type="text" id="username" name="username"></td></tr>
		<tr><td>Password</td><td><input type="password" name="password" id="password"></td></tr>
		<tr><td colspan="2"><input type="submit" id="submit" name="submit"></td><tr>
	</table>
</form>
