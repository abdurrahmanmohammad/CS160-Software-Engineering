<?php
/**
 * ###################################################################################
 * ########## Database security, sanitization, and general purpose methods  ##########
 * ###################################################################################
 */

/**
 * Function to notify the user when error occurs
 * @param $error
 */
function mysql_fatal_error($error) {
	Alert($error); // Display error as a popup
	echo "<br>$error<br>"; // Print out the error: for debugging purposes
	die("We are sorry, but it was not possible to complete the requested task.
    Please click the back button on your browser and try again.
    Thank you!");
}

/**
 * Passes each item it retrieves through the real_escape_string method
 * of the connection object to strip out any characters that a hacker
 * may have inserted in order to break into or alter your database
 * @param $conn
 * @param $var
 * @return mixed
 */
function get_post($conn, $var) {
	return $conn->real_escape_string($_POST[$var]);
}

/**
 * Sanitizes input string for MySQL
 * @param $conn
 * @param $var
 * @return string
 */
function sanitizeMySQL($conn, $var) {
	return sanitizeString($conn->real_escape_string($var));
}

/**
 * Helper method for sanitizeMySQL
 * @param $var
 * @return string
 */
function sanitizeString($var) {
	return htmlentities(strip_tags(stripslashes($var)));
}

/**
 * Create connection to DB and return connection
 * @return mysqli
 */
function getConnection() {
	require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
	$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	return $conn; // Return connection
}


/**
 * A handy function to destroy a session and its data
 */
function destroy_session_and_data() {
	session_start();
	$_SESSION = array(); // Delete all the information in the array
	setcookie(session_name(), '', time() - 2592000, '/');
	session_destroy();
}

/**
 * Destroys session when logged in as a different person
 */
function different_user() {
	destroy_session_and_data(); // Destroy session
	header("Location: Signin.php"); // Go to sign in page
}

/**
 * Authenticates user and manages the user session
 * @return mixed|null
 */
function authenticate($account_type) {
	session_start(); // Start session
	/** Check if logout */
	if(isset($_POST['logout'])) {
		destroy_session_and_data(); // Logs out user by destroying session
		return null;
	}
	/** Preventing session fixation */
	if(!isset($_SESSION['initiated'])) {
		session_regenerate_id();
		$_SESSION['initiated'] = 1;
	}
	if(!isset($_SESSION['count'])) $_SESSION['count'] = 0;
	else ++$_SESSION['count'];
	/** Initialize session */
	$account = null;
	if(isset($_SESSION['account'])) { // If the account is not stored in session
		/** Preventing session hijacking: different user */
		if($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) different_user();
		$account = $_SESSION['account']; // Retrieve account from session
	} else {
		destroy_session_and_data(); // Destroy session and go to sign in page
		header("Location: ../Signin.php?logout=true"); // Go to sign in page
	}

	// If page is for admin ($account_type == 'admin) and a customer tries accessing it: go to customer portal homepage
	// If page is for customer ($account_type == 'customer) and an admin tries accessing it: go to admin portal homepage
	if(strcmp($account_type, 'admin') === 0 && strcmp($account['account_type'], "customer") === 0)
		header("Location: ../Customer/CustomerPortal.php"); // Customer cannot use admin portal
	else if(strcmp($account_type, 'customer') === 0 && strcmp($account['account_type'], 'admin') === 0)
		header("Location: ../Admin/AdminPortal.php"); // Admin cannot use customer portal
	else if(strcmp($account_type, 'admin') !== 0 && strcmp($account_type, 'customer') !== 0) // If invalid account
		header("Location: ../Signin.php?logout=true"); // Logout if account is not an admin nor a customer
	return $account;
}

/**
 * Prints a popup message
 * @param $message
 */
function Alert($message) {
	echo "<script>alert('$message');</script>"; // Display the alert box
}