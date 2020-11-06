<?php
require_once 'DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/**
 * #######################################################################################
 * ########## Picture: Picture(itemID, directory)                               ##########
 * ########## itemID: CHAR(4) # Store a 4 digit ID                              ##########
 * ########## directory: VARCHAR(200) # Store a 200 character or less directory ##########
 * #######################################################################################
 */

/**
 * @param $conn
 * @return null
 */
function PictureInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Picture table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Picture ( 
		itemID CHAR(4) NOT NULL, 
		directory VARCHAR(200) NOT NULL,
		PRIMARY KEY (itemID, directory)
	);";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Check if statement was executed. If cannot build table, terminate program.
}

function PictureSearch($conn, $itemID) {
	if($stmt = $conn->prepare("SELECT * FROM Picture WHERE itemID=?;")) {
		$stmt->bind_param("s", $itemID);
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

function PictureInsert($conn, $itemID, $directory) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($itemID) || is_null($directory)) return false; // If blank fields
	if(PictureExists($conn, $itemID, $directory) != 0) return false; // If item already exists
	if($stmt = $conn->prepare("INSERT INTO Picture VALUES (?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ss', $itemID, $directory); // Bind params for sanitization
		$stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function PictureDelete($conn, $itemID, $directory) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("DELETE FROM Picture WHERE itemID=? AND directory=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ss', $itemID, $directory); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function PictureExists($conn, $itemID, $directory) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Picture WHERE itemID=? AND directory=?;")) {
		$stmt->bind_param("ss", $itemID, $directory);
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}

