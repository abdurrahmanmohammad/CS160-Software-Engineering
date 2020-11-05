<?php

// Account(email, password, accountType, first name, last name, phone, address)
// Note: Don't forget to have a create admin account page in the account management page

function checkPassword($conn, $un_temp, $pw_temp) {
	$query = "SELECT * FROM Account WHERE username='$un_temp'";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
	elseif($result->num_rows) { // If user exists
		$row = $result->fetch_array(MYSQLI_NUM);
		$result->close();
		if(password_verify($pw_temp, $row[1])) { // Check password
			session_start();
			ini_set('session.gc_maxlifetime', 60 * 60 * 24); // ##### Setting a Time-Out for cookie #####
			$_SESSION['username'] = $un_temp;
			$_SESSION['password'] = $pw_temp;
			$_SESSION['email'] = $row[2];
			/* Additional Checks: Preventing session hijacking */
			$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
			echo "Hi $row[0], you are now logged in!";
			die("<p><a href=user.php>Click here to continue</a></p>");
		} else
			die("Invalid username/password combination"); // Incorrect password
	} else
		die("Invalid username/password combination"); // Incorrect username
}


/**
 * Builds the table of users
 */
function AccountInitialize($conn) {
	// User(email, password, accountType, first name, last name, phone, address)
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Account table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Account (
		email VARCHAR(50) NOT NULL,
        password CHAR(60) NOT NULL,
        accountType VARCHAR(10) NOT NULL,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        phone CHAR(10) NOT NULL,
        address VARCHAR(100) NOT NULL,
        PRIMARY KEY (email)
	)";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Error not printed (for debugging)
	return true;
}

function AccountInsert($conn, $email, $password, $accountType, $firstname, $lastname, $phone, $address) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($email) || is_null($password) || is_null($accountType) || is_null($firstname) || is_null($lastname) || is_null($phone) || is_null($address)) return false; // If blank fields
	if(AccountExists($conn, $email, $password, $accountType, $firstname, $lastname, $phone, $address) != 0) return false; // If item already exists
	if($stmt = $conn->prepare("INSERT INTO Account VALUES (?, ?, ?, ?, ?, ?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('sssssss', $email, $password, $accountType, $firstname, $lastname, $phone, $address); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function AccountDelete($conn, $email) {
	if(is_null($conn) || is_null($email)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("DELETE FROM Account WHERE email=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $email); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function AccountExists($conn, $email) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Account WHERE email=?;")) {
		$stmt->bind_param("s", $email); /* bind parameters for markers */
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}

function AccountUpdate($conn, $OLD_email, $email, $password, $accountType, $firstname, $lastname, $phone, $address) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($email) || is_null($password) || is_null($accountType) || is_null($firstname) || is_null($lastname) || is_null($phone) || is_null($address)) return false; // If blank fields
	if(AccountExists($conn, $OLD_email) == 0) return false; // If account does not exist
	if(AccountExists($conn, $email) != 0) return false; // If account with new email exists
	if($stmt = $conn->prepare("Update Account SET email=?, password=?, accountType=?, firstname=?, lastname=?, phone=?, address=? WHERE email=?")) { // Placeholders for further sanitization
		$stmt->bind_param('ssssssss', $email, $password, $accountType, $firstname, $lastname, $phone, $address, $OLD_email); // Bind params for sanitization
		$stmt->execute(); // Execute query
		$affected_rows = $stmt->affected_rows; // Store number of rows affected
		$stmt->close();
		return $affected_rows;
	} else die(mysql_fatal_error("Could not update Account: ".$conn->error)); // Error with prepare statement
}

function AccountSearch($conn, $email) {
	if(is_null($conn) || is_null($email)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Account WHERE email=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $email); // Bind params for sanitization
		$stmt->execute();
		$result = $stmt->get_result();
		$output = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		$result->close(); // Close statement for security
		return $output; // Return the result as a 2D array
	}
}