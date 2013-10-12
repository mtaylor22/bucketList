<?php
session_start();
if (isset($_POST['submit'])){
	mysql_connect("localhost", "admin", "ping") or die(mysql_error());
	mysql_select_db("jar_project") or die(mysql_error());
	$query = "SELECT * FROM users where username = '". $_POST['username'] ."'"; 
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result) or die(mysql_error());
	if ($row['password'] == $_POST['password']){
		$_SESSION['loggedin'] = True;
		$_SESSION['username'] = $_POST['username'];
	}
}
if (isset($_SESSION['loggedin'])){
	print '<h1>Hi, '. $_SESSION['username'] . '</h1>';

}
?>

<form method="POST" action="login.php">
	<table style="text-align:center;">
		<tr><td>Username</td><td><input type="text" id="username" name="username"></td></tr>
		<tr><td>Password</td><td><input type="password" name="password" id="password"></td></tr>
		<tr><td colspan="2"><input type="submit" id="submit" name="submit"></td><tr>
	</table>
</form>
