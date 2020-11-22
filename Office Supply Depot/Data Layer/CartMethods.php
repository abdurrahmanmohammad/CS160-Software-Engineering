<?php
/**
 * ###################################################################################
 * ########## Carts(userID, itemID, multiplicity)                    ##########
 * ########## email: VARCHAR(50) # Store email (mac 50 chars)               ##########
 * ########## itemID: CHAR(4) # Store a 4 digit user ID                     ##########
 * ########## multiplicity: SMALLINT # Store a multiplicity of item in cart ##########
 * ###################################################################################
 */

function InitializeCarts($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create Accounts table */
	$query = "CREATE TABLE IF NOT EXISTS Carts ( 
		email VARCHAR(50) NOT NULL,
		itemID CHAR(4) NOT NULL, 
		multiplicity SMALLINT NOT NULL,
		PRIMARY KEY (email, itemID)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Carts table: ".$conn->error);
	return true; // Successfully built table
}

function InsertCart($conn, $email, $itemID, $multiplicity = 0) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Carts VALUES (?, ?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ssd', $email, $itemID, $multiplicity)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

function DeleteCart($conn, $email) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Carts WHERE email=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $email)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function DeleteCartItem($conn, $email, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Carts WHERE email=? AND itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $email, $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function SearchCart($conn, $email) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	/* Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Carts WHERE email=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $email)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
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

function UpdateCart($conn, $email, $itemID, $multiplicity) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$multiplicity) mysql_fatal_error("Multiplicity cannot be null");
	/** Update: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("Update Carts SET multiplicity=? WHERE email=? AND itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('dss', $multiplicity, $email, $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$affected_rows = $stmt->affected_rows; // Store number of rows affected
	$stmt->close(); // Close statement
	return $affected_rows; // Return true after successful update
}

/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

/**
 * Checks if a certain cart exists
 * Update: Use the search method instead
 * @param $conn
 * @param $email
 * @param $itemID
 * @return mixed
 */
function CartExists($conn, $email, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Perform query */
	if(!$stmt = $conn->prepare("SELECT * FROM Carts WHERE email=? AND itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $email, $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Store result */
	if(!$stmt->store_result()) mysql_fatal_error("Store result failed : ".$conn->error);
	$rowcount = $stmt->num_rows; // Get rowcount
	$stmt->close(); // Close statement
	return $rowcount; // Success
}