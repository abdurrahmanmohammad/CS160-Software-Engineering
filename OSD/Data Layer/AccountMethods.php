<?php


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
	if($OLD_email != $email && AccountExists($conn, $email) != 0) return false; // If new email and account with new email exists
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

function AccountSearchType($conn, $accountType) {
	if(is_null($conn) || is_null($accountType)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Account WHERE accountType=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $accountType); // Bind params for sanitization
		$stmt->execute();
		$result = $stmt->get_result();
		$output[] = array(); // 2D array to store query output (array of rows)
		if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
		elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
			for($i = 0; $i < $rows; $i++) { // Store all entries in table
				$result->data_seek($i); // Get the i^th row
				$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
			}
		// If no rows returned, don't store anything
		$result->close(); // Close statement for security
		return $output; // Return the result as a 2D array
	}
}