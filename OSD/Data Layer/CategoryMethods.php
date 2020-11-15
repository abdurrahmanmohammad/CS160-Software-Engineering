<?php
require_once 'DatabaseMethods.php'; // Load methods for error and sanitization

/**
 * #######################################################################
 * ########## Category(itemID, category)                        ##########
 * ########## itemID: CHAR(4) # Store a 4 digit ID              ##########
 * ########## category: VARCHAR(20) # Store <= 20 char category ##########
 * #######################################################################
 */

function CategoryInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Category table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Category ( 
		itemID CHAR(4) NOT NULL, 
		category VARCHAR(20) NOT NULL,
		PRIMARY KEY (itemID, category)
	);";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Check if statement was executed. If cannot build table, terminate program.
}

function CategorySearch($conn, $itemID) {
	if($stmt = $conn->prepare("SELECT * FROM Category WHERE itemID=?;")) {
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


function CategoryInsert($conn, $itemID, $category) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($itemID) || is_null($category)) return false; // If blank fields
	if(CategoryExists($conn, $itemID, $category) != 0) return false; // If item already exists
	if($stmt = $conn->prepare("INSERT INTO Category VALUES (?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ss', $itemID, $category); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function CategoryDelete($conn, $itemID, $category) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("DELETE FROM Category WHERE itemID=? AND category=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ss', $itemID, $category); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function CategoryDeleteItemID($conn, $itemID) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("DELETE FROM Category WHERE itemID=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $itemID); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return $output; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function CategoryExists($conn, $itemID, $category) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Category WHERE itemID=? AND category=?;")) {
		$stmt->bind_param("ss", $itemID, $category);
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}

function getCategories($conn) {
	if(!$conn) return null; // DB connection must be passed in
	$result = $conn->query("SELECT DISTINCT category FROM Category;"); // Execute query statement
	$output[] = array(); // 2D array to store query output (array of rows)
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
	elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		}
	$result->close(); // Close statement for security
	return $output; // Return the result as a 2D array
}

function CategoryGetItemsByCategory($conn, $category) {
	if($stmt = $conn->prepare("SELECT * FROM Item NATURAL JOIN Category WHERE category=?;")) {
		$stmt->bind_param("s", $category);
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