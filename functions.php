

<?php

//Connect to database
/*
	Input :
	
	Output :

	Description :
*/
function connectToDatabase(){
		mysql_connect("localhost", "admin", "ping") or die(mysql_error());
		mysql_select_db("jar_project") or die(mysql_error());
}

//Verify Credentials
/*
	Input:

	Output:

	Description:
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

	Output : Our user's row from the user table

	Description :
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
	Return: A list of the ideas from the jar. Each entry in the list is a row from the idea table.
	Input: A list of attributes needed to fetch the jar. It will be a list of attributes, but it will be a username for now.	   

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
	
	Inputs : 

	Outputs :

	Description :
*/
function createIdea($ideaText, $username, $parentJar){

	//Connect to our database
	connectToDatabase();

	//Select the user passed into the function, it should be the logged in user of the session
	$result = mysql_query("SELECT * FROM users WHERE username = '".$username."' ") or die(mysql_error());
	$user = mysql_fetch_array($result);

	//Add our user to our attribute list and turn into comma seperated list

	//Add our idea to the database
	mysql_query("INSERT INTO ideas (idea, parent_jar) VALUES('". $ideaText ."', '". $parentJar ."' ) ") or die(mysql_error());
}

//GET ALL JARS
/*
	jars[] getAllJars(username);
	
	Inputs : A user's ID aka their username
	Output : A list of all of the names of jars owned by our user
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
	void chooseIdea(string attributes);
	
	Inputs : A comma seperated list of attributes so that a proper idea is chosen

	Output :

	Description :
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
	void removeIdea(ideaID, jarQuery, username);
	
	Inputs : A string of text that describes the idea, the user's ID, and attributes 
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
	
	Inputs : A string to represent our jar name and a list of attributes to add to the jar 
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


//REMOVE JAR
/*
	void addIdea(ideaText, username, attributes);
	
	Inputs : A string of text that describes the idea, the user's ID, and attributes 
*/
function deleteJar($username, $jarName){

	connectToDatabase();

	$result = mysql_query("SELECT * FROM users WHERE username = '".$username."' ") or die(mysql_error());
	$user = mysql_fetch_array($result);
	
	$userJarNames = explode(",", $user['jar_names']);

	$removalIndex = array_search($jarName, $userJarNames);
	if ($removalIndex != false){
    	unset($userJarNames[$removalIndex]);
	}

	$commaSeperatedNames = implode(",", $userJarNames);

	//Add our idea to the database
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
