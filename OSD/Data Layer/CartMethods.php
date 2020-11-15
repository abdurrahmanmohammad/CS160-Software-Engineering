<?php
/**
 * #######################################################################################
 * ########## Cart: Cart(userID, itemID, multiplicity)                      ##########
 * ########## email: VARCHAR(50) # Store a 50 char email                    ##########
 * ########## itemID: CHAR(4) # Store a 4 digit user ID                     ##########
 * ########## multiplicity: SMALLINT # Store a multiplicity of item in cart ##########
 * #######################################################################################
 */

function CartInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Cart table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Cart ( 
		email VARCHAR(50) NOT NULL,
		itemID CHAR(4) NOT NULL, 
		multiplicity SMALLINT NOT NULL,
		PRIMARY KEY (email, itemID)
	);";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Check if statement was executed. If cannot build table, terminate program.
}

function CartSearch($conn, $email) {
	if($stmt = $conn->prepare("SELECT * FROM Cart WHERE email=?;")) {
		$stmt->bind_param("s", $email);
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
	return null;
}

function CartInsert($conn, $email, $itemID, $multiplicity = 1) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($email) || is_null($itemID)) return false; // If blank fields
	if(CartExists($conn, $email, $itemID) != 0) return false; // If item already exists
	if($stmt = $conn->prepare("INSERT INTO Cart VALUES (?, ?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ssd', $email, $itemID, $multiplicity); // Bind params for sanitization
		$stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function CartExists($conn, $email, $itemID) {
	if(is_null($conn) || is_null($email) || is_null($itemID)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Cart WHERE email=? AND itemID=?;")) {
		$stmt->bind_param("ss", $email, $itemID);
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}

function CartDelete($conn, $email, $itemID) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("DELETE FROM Cart WHERE email=? AND itemID=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param("ss", $email, $itemID);
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful delete
	}
	return false; // If prepared statement failed, return false
}

function CartUpdate($conn, $email, $itemID, $multiplicity) {
	if(is_null($conn) || is_null($email) || is_null($itemID) || is_null($multiplicity)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("Update Cart SET multiplicity=? WHERE email=? AND itemID=?;")) { // Placeholders for further sanitization
		$stmt->bind_param('dss', $multiplicity, $email, $itemID); // Bind params for sanitization
		$stmt->execute(); // Execute query
		$affected_rows = $stmt->affected_rows; // Store number of rows affected
		$stmt->close();
		return $affected_rows;
	} else die(mysql_fatal_error("Could not update Cart table: ".$conn->error)); // Error with prepare statement

}