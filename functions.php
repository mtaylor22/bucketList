<?php
	function connect(){
		mysql_connect("localhost", "admin", "ping") or die(mysql_error());
		mysql_select_db("jar_project") or die(mysql_error());
	}

?>