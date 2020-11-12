<?php

// ********** Error & Sanitization Methods **********
/**
 * Function to notify the user when error occurs
 */
function mysql_fatal_error($error) {
	/*echo <<<_END
    We are sorry, but it was not possible to complete the requested task.
    Please click the back button on your browser and try again.
    Thank you.
    _END;*/
	echo "<br>".$error; // Print out the error ***(for debugging purposes only)***
	return "We are sorry, but it was not possible to complete the requested task.
    Please click the back button on your browser and try again.
    Thank you.";

}

/**
 * Passes each item it retrieves through the real_escape_string method of the connection object to strip
 * out any characters that a hacker may have inserted in order to break into or alter your database
 */
function get_post($conn, $var) {
	return $conn->real_escape_string($_POST[$var]);
}

/**
 * Helper method for sanitizeMySQL
 */
function sanitizeString($var) {
	$var = stripslashes($var);
	$var = strip_tags($var);
	$var = htmlentities($var);
	return $var;
}

/**
 * Sanitizes input string for MySQL
 */
function sanitizeMySQL($conn, $var) {
	$var = $conn->real_escape_string($var);
	$var = sanitizeString($var);
	return $var;
}

/**
 * A handy function to destroy a session and its data
 */
function destroy_session_and_data() {
	session_start();
	$_SESSION = array();    // Delete all the information in the array
	setcookie(session_name(), '', time() - 2592000, '/');
	session_destroy();
}

/**
 * When logged in as a different person
 */
function different_user() {
	destroy_session_and_data(); // Destroy session
	header("Location: SignIn.php"); // Go to sign in page
}



// ********** Fix **********
function authenticate() {
	session_start(); // Start session
	//if(isset($_POST['Logout'])) destroy_session_and_data(); // Logs out user (if click log out button)

	/* Preventing session fixation */
	if(!isset($_SESSION['initiated'])) {
		session_regenerate_id();
		$_SESSION['initiated'] = 1;
	}
	if(!isset($_SESSION['count'])) $_SESSION['count'] = 0;
	else ++$_SESSION['count'];

	/* Initialize session */
	if(isset($_SESSION['account'])) { // If the account is not stored in session
		/* Preventing session hijacking */
		if($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']))
			different_user();
		$account = $_SESSION['account']; // Retrieve account from session
		return $account;
	} else {
		destroy_session_and_data(); // Destroy session and go to sign in page
		header("Location: SignIn.php"); // Go to sign in page
	}
	return null;
}
