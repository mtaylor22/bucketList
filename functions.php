

<?php

//Connect to database
/*
	Input : None
	
	Output : None

	Description : Connect to our project's database on the sql server.
*/
function connectToDatabase(){
		mysql_connect("localhost", "admin", "ping") or die(mysql_error());
		mysql_select_db("jar_project") or die(mysql_error());
}

//Verify Credentials
/*
	Input: A string to represent the entered username and a string to represent the entered password

	Output: True if the credentials are valid and false if the credentials do not match any in the database.

	Description: Verify that the username and password entered match a record in our user table.
*/
function verifyCredentials($username, $password){
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

//GET LOGGED USER
/*
	Input : None

	Output : The logged in user's row from the user table.

	Description : Find the row from the user table of the user that is logged in.
*/
function getLoggedUser(){
	//If the user is logged in, fetch our user's row in the table and return it
	if ( isset($_SESSION['loggedin']) ){
		$result = mysql_query("SELECT * FROM users where username = '". $_SESSION['username'] ."'") or die(mysql_error());
		$row = mysql_fetch_array($result) or die(mysql_error());
		return $row;
	}
	else return false;
}

//GET JAR
/*
	Input : A list of attributes needed to fetch the jar. It will be a list of attributes, but it will be a username for now.	   
	
	Output : A list of the ideas from the jar. Each entry in the list is a row from the idea table.

	Description: Get a list of all of the ideas from the user's selected jar.
*/
function getJar($jarName){

	$resultArray = array();
	connectToDatabase();
	//Grab all of our ideas
	$result = mysql_query("SELECT * FROM ideas") or die(mysql_error());	    
    //For each individual idea, check if it belongs in our user's jar and add it to result array if so
    while( $ideaRow = mysql_fetch_array($result) ){
		
		$parentOfCurrent = $ideaRow['parent_jar'];

		if( $jarName == $parentOfCurrent ){
			$resultArray[] = $ideaRow;
		}		
	}
	return $resultArray; 
}

//CREATE IDEA
/*
	void createIdea(ideaText, username, attributes);
	
	Inputs : A string to represent a description of the idea, and a string to represent the parent jar of the idea
			 being added to the database.

	Outputs : None

	Description : Create an entry in the ideas table with a description of the idea along with the name of the jar the
				  idea belongs in.
*/
function createIdea($ideaText, $parentJar){

	//Connect to our database
	connectToDatabase();

	//Add our idea to the database
	mysql_query("INSERT INTO ideas (idea, parent_jar) VALUES('". $ideaText ."', '". $parentJar ."' ) ") or die(mysql_error());
}

//GET ALL JARS
/*	
	Input : A user's ID aka their username

	Output : A list of all of the names of jars owned by our user

	Description : Given a username, this will produce the user's "jar ownership list". Each element
				  in a jar ownership list will be a string representing the name of a jar.
*/
function getAllJars($username){

	$returnQueries = array();
	$returnNames = array();
	$returnArray = array();
	//Connect to our database
	connectToDatabase();

	//For every one of our user's jars ... we want to get all of the names of these jars
	$result = mysql_query("SELECT * FROM users WHERE username = '".$username."' ") or die(mysql_error());
	$user = mysql_fetch_array($result);

	$returnArray = explode(",", $user['jar_names']);

	return $returnArray;
}

//CHOOSE IDEA
/*
	Inputs : The name of the jar from which to randomly choose ideas from.

	Output : The idea that is chosen at random.

	Description : Find all of the ideas from the idea table that belong in our given jar.
				  From these ideas, choose one at random and return the entire idea row for use.
*/
function chooseIdea($jarName){
	//Connect to our database
	connectToDatabase();
	//Call getJar with our user's currently selected jar
	$currentJar = getJar($jarName);
	//Grab a random index from our list of ideas
	$chosenIndex = array_rand($currentJar,1);
	//Save idea with this index for return
	$chosenIdea = $currentJar[$chosenIndex];

	return $chosenIdea;
}

//REMOVE IDEA
/*	
	Input : A string of text that describes the idea, the user's ID, and attributes 

	Output : None

	Description : Find the idea with the given ideaID and remove it from the idea table.
*/
function removeIdea($ideaID){
	//Connect to our database
	connectToDatabase();
	//Delete the given idea from the ideas table
	mysql_query("DELETE * FROM ideas WHERE id = '".$ideaID."' ") or die(mysql_error());
}


//CREATE JAR
/*
	void createJar(jarName, jarQuery);
	
	Inputs : A string for the user's identification (username) and a string for the name of the jar to be created.

	Output : None

	Description : Add the name of the jar given to our user's "jar ownership table".
*/
function createJar($username, $jarName){

	connectToDatabase();

	//Grab our user to modify their row in the table
	$result = mysql_query("SELECT * FROM users WHERE username = '".$username."' ") or die(mysql_error());
	$user = mysql_fetch_array($result);

	$userJarNames = explode(",", $user['jar_names']);

	$userJarNames[] = $jarName;

	$commaSeperatedNames = implode(",", $userJarNames);

	//Add our idea to the database
	mysql_query("UPDATE users SET jar_names = '".$commaSeperatedNames."' WHERE username = '".$username."'") or die(mysql_error());
}


//DELETE JAR
/*	
	Input : A string for the user's identification (username) and a string for the name of the jar to be deleted.

	Output : None

	Description : Remove all of the ideas contained in the jar from the idea table and then delete this jar
				  from the "jar ownership table" of our user.
*/
function deleteJar($username, $jarName){

	connectToDatabase();
	//For each of our ideas in the jar, find it in the table and remove this row
	$ideas = getJar($jarName);
	foreach($ideas as $idea){
		removeIdea($idea['id']);
	}
	//Retrieve our user's row in the table to remove our jar name from the user's ownership list
	$result = mysql_query("SELECT * FROM users WHERE username = '".$username."' ") or die(mysql_error());
	$user = mysql_fetch_array($result);

	//Create an array out of our comma seperated jar name list
	$userJarNames = explode(",", $user['jar_names']);

	//Find the index of the jar to be deleted from the array, and then remove it from the ownership list
	$removalIndex = array_search($jarName, $userJarNames);
	if ($removalIndex != false){
    	unset($userJarNames[$removalIndex]);
	}

	//Turn the jar name array back into a comma seperated list
	$commaSeperatedNames = implode(",", $userJarNames);
	
	//Update our user's ownership list after removing our deleted jar from it
	mysql_query("UPDATE users SET jar_names = '".$commaSeperatedNames."' WHERE username = '".$username."'") or die(mysql_error());

}

//BAN IDEA
/*
	void addIdea(ideaText, username, attributes);
	
	Inputs : A string of text that describes the idea, the user's ID, and attributes 
*/

//SET COMPLETE

/*
	void addIdea(ideaText, username, attributes);
	
	Inputs : A string of text that describes the idea, the user's ID, and attributes 
*/

?>
