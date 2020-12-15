<?php
require_once 'DatabaseMethods.php';
/**
 * ###################################################################################################
 * ########## Accounts(email, password, AccountsType, first name, last name, phone, address)##########
 * ########## email VARCHAR(50)         # Store email (max: 50 chars)                       ##########
 * ########## password CHAR(60)         # Hash of password (60 chars)                       ##########
 * ########## account_type VARCHAR(10)  # Options: admin, customer                          ##########
 * ########## firstname VARCHAR(50)     # Store first name (max: 50 chars)                  ##########
 * ########## lastname VARCHAR(50)      # Store last name (max: 50 chars)                   ##########
 * ########## phone CHAR(10)            # Store 10 digit phone number                       ##########
 * ########## address VARCHAR(100)      # Store max 100 chars of address                    ##########
 * ###################################################################################################
 */

/**
 * Builds the table of users
 * @param $conn
 * @return bool
 */
function InitializeAccounts($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Accounts (
		email VARCHAR(50) NOT NULL,
        password CHAR(60) NOT NULL,
        account_type VARCHAR(10) NOT NULL,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        phone CHAR(10) NOT NULL,
        address VARCHAR(100) NOT NULL,
        PRIMARY KEY (email)
	)";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

/**
 * Inserts an account in the Accounts table
 * Assertion: account with same email does not already exist
 * @param $conn
 * @param $email
 * @param $password
 * @param $account_type
 * @param $firstname
 * @param $lastname
 * @param $phone
 * @param $address
 * @return bool|void
 */
function InsertAccount($conn, $email, $password, $account_type, $firstname, $lastname, $phone, $address) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$password) mysql_fatal_error("Password cannot be null");
	if(!$account_type) mysql_fatal_error("Account type cannot be null");
	if(!$firstname) mysql_fatal_error("First name cannot be null");
	if(!$lastname) mysql_fatal_error("Last name cannot be null");
	if(!$phone) mysql_fatal_error("Phone cannot be null");
	if(!$address) mysql_fatal_error("Address cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Accounts VALUES (?, ?, ?, ?, ?, ?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('sssssss', $email, $password, $account_type, $firstname, $lastname, $phone, $address)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

/**
 * Deletes an account
 * Assertion: conn is a valid DB connection
 * @param $conn
 * @param $email
 * @return bool|void
 */
function DeleteAccount($conn, $email) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Accounts WHERE email=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $email)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}


/**
 * Searches and returns an account by email
 * Assertion: conn is a valid DB connection
 * @param $conn
 * @param $email
 * @return null
 */
function SearchAccountByEmail($conn, $email) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Accounts WHERE email=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $email)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get result */
	$result = $stmt->get_result(); // Get query result
	if($result->num_rows == 0) return null; // If result is empty, return null
	$output = $result->fetch_array(MYSQLI_BOTH); // Convert result into an associative array
	$stmt->close(); // Close statement
	return $output; // Return output
}


function SearchAccountByAccountType($conn, $account_type) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$account_type) mysql_fatal_error("Account type cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Accounts WHERE account_type=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $account_type)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get result */
	$result = $stmt->get_result(); // Get query result
	if($result->num_rows == 0) return null; // If result is empty, return null
	/** Store each row of result in output */
	$output[] = array(); // 2D array to store query output (array of rows)
	if(!$result) mysql_fatal_error($conn->error); // Error: execute custom error function
	elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		}
	$stmt->close(); // Close statement
	return $output; // Return output
}

/**
 * Updates an account specified by email
 * Assertion: account exists
 * @param $conn
 * @param $email
 * @param $password
 * @param $account_type
 * @param $firstname
 * @param $lastname
 * @param $phone
 * @param $address
 * @return false|void
 */
function UpdateAccount($conn, $email, $password, $account_type, $firstname, $lastname, $phone, $address) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$password) mysql_fatal_error("Password cannot be null");
	if(!$account_type) mysql_fatal_error("Account type cannot be null");
	if(!$firstname) mysql_fatal_error("First name cannot be null");
	if(!$lastname) mysql_fatal_error("Last name cannot be null");
	if(!$phone) mysql_fatal_error("Phone cannot be null");
	if(!$address) mysql_fatal_error("Address cannot be null");
	/** Update: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("Update Accounts SET password=?, account_type=?, firstname=?, lastname=?, phone=?, address=? WHERE email=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('sssssss', $password, $account_type, $firstname, $lastname, $phone, $address, $email)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$affected_rows = $stmt->affected_rows; // Store number of rows affected
	$stmt->close(); // Close statement
	return $affected_rows; // Return number of rows affected
}

/**
 * Retrieves inputted email and password and verifies account and password
 * @param $conn
 * @return void|null
 */
function CheckPassword($conn) {
	/** Retrieve inputs */
	$account = SearchAccountByEmail($conn, get_post($conn, 'email')); // Retrieve account from DB using email
	/** Checks: account should exist with associated email and hashed passwords should match */
	if(!$account) return null; // User does not exist
	elseif(!password_verify(get_post($conn, 'password'), $account['password'])) return; // Invalid password
	/** Create and start session */
	session_start();
	ini_set('session.gc_maxlifetime', 60 * 60 * 24); // Setting a Time-Out for cookie
	$_SESSION['account'] = $account; // Store account info in session
	/** Prevent session hijacking */
	$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
	return $account; // User account and password are authenticated
}

/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

/**
 * Checks if an account exists
 * Update: Use the search method instead
 * @param $conn
 * @param $email
 * @return mixed
 */
function AccountExists($conn, $email) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	/* Perform query */
	if(!$stmt = $conn->prepare("SELECT * FROM Accounts WHERE email=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	/* Bind parameters for sanitization */
	if(!$stmt->bind_param('s', $email)) mysql_fatal_error("Binding parameters failed: ".$conn->error);
	/* Execute statement */
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error);
	/* Store result */
	if(!$stmt->store_result()) mysql_fatal_error("Store result failed : ".$conn->error);
	/* Get rowcount */
	$rowcount = $stmt->num_rows;
	/* Close statement */
	$stmt->close();
	return $rowcount; // Return true after successful insert
}