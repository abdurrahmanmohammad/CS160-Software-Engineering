<?php
/*
For not submitted
	Print page

If form submitted
	Correct input
		Check is user already exists
			Print confirmation message if successfully created
				Go to login page
	Invalid input
		User already exists (same username)
			PK is userID, username, email
		*Password is not secure
		*Address - can be null or anything
		*Phone number is invalid - should e detected by HTML form
		*Email is invalid - should e detected by HTML form

		Inform user of error - JS message
			Print page
*/
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection


if(isset($_POST['email']) && isset($_POST['password'])
	&& isset($_POST['firstname']) && isset($_POST['lastname'])
	&& isset($_POST['phone']) && isset($_POST['address'])) {
	$password = password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)

	if(AccountInsert($conn, get_post($conn, 'email'), $password, "customer",
		get_post($conn, 'firstname'), get_post($conn, 'lastname'),
		get_post($conn, 'phone'), get_post($conn, 'address')))
		header("Location: index.php");
	else
		echo "Account exists!";
}


printCreateUser();


/**
 * Prints webpage to create user
 */
function printCreateUser() {
	echo <<<_END
	<form action="Register.php" method="post" enctype='multipart/form-data'>
	<pre>
	Register
	
	Email <input type="email" name="email" required>
	Password <input type="text" name="password" minlength="8" required>
	First name <input type="text" name="firstname" minlength="2" required>
	Last name <input type="text" name="lastname" minlength="2" required>
	Phone <input type="tel" name="phone" pattern="[0-9]{10)" placeholder="(xxx) xxx-xxxx" required>
	Address <input type="text" name="address" required>
	<input type="submit" value="Create">
	
	</pre>
	</form>
	_END;
}