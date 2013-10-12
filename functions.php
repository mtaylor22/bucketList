<?php
	function connect(){
		mysql_connect("localhost", "admin", "ping") or die(mysql_error());
		mysql_select_db("jar_project") or die(mysql_error());
	}
	function check_login($username, $password){
		$query = "SELECT * FROM users where username = '". $username ."'"; 
		$result = mysql_query($query) or die(mysql_error());
		if ($result == false) return false;
		$row = mysql_fetch_array($result) or die(mysql_error());
		if ($row['password'] == $password){
			$_SESSION['loggedin'] = True;
			$_SESSION['username'] = $username;
			return true;
		}
		return false;
	}
	function get_logged_user(){
		if (isset($_SESSION['loggedin'])){
			$query = "SELECT * FROM users where username = '". $_SESSION['username'] ."'"; 
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result) or die(mysql_error());
			return $row;
		} else{
			return false;
		}
	}
	function getJar($attributes){
		$query = "SELECT * FROM ideas";
		$result = mysql_query($query) or die(mysql_error());
		$ideas = array();
		while($row = mysql_fetch_array($result)){
			$attr_array = explode(',', $row['attr']);
			if (in_array($attributes, $attr_array)){
				array_push($ideas, $row);
			}
		}
		return $ideas;
	}
?>