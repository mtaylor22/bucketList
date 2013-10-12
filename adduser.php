<h1>Add User:</h1>
<form method="POST" action="adduser.php">
	<table style="text-align:center;">
		<tr><td>Username</td><td><input type="text" id="username" name="username"></td></tr>
		<tr><td>Password</td><td><input type="password" name="password" id="password"></td></tr>
		<tr><td colspan="2"><input type="submit" id="submit" name="submit"></td><tr>
	</table>
</form>
<?php

if (isset($_POST['submit'])){
	mysql_connect("localhost", "admin", "ping") or die(mysql_error());
	mysql_select_db("jar_project") or die(mysql_error());
	mysql_query("INSERT INTO users (username, password) VALUES('". $_POST['username'] ."', '". $_POST['password'] ."' ) ") or die(mysql_error());
	print 'User "'. $_POST['username'] .'" added!';
}
?>