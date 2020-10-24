<?php

/**
 * Table: Item(itemID, title, price, weight, description, picture)
 * itemID: CHAR(4) # Store a 4 digit ID
 * title: VARCHAR(50) # Store a 50 character or less title
 * price: DECIMAL(8,2) # Max price: 999,999.99
 * weight: DECIMAL(8,2) # Max weight: 999,999.99
 * description: TEXT # Store paragraphs of item description
 * picture: VARCHAR(255) # Store directory of picture
 */
/* Import Login.php, additional methods, & test connection to DB */
require_once 'DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/**
 * Method to search/query an item in the DB
 * @param $conn
 * @param null $itemID
 * @param null $title
 * @param null $price
 * @param null $weight
 * @param null $description
 * @param null $picture
 * @return mixed
 */

// ****** Have a pictures table *****
function ItemSearch($conn, $itemID, $title, $price, $weight, $description, $picture) {
	if(!$conn) return null; // DB connection must be passed in
	ItemSanitizeVariables($conn, $itemID, $title, $price, $weight, $description, $picture); // Sanitize variables by reference
	$stmt = "SELECT * FROM Table".ItemAddConditions($itemID, $title, $price, $weight, $description, $picture).";"; // Add var conditions and ; to end statement
	$result = $conn->query($stmt); // Execute query statement
	$output[] = array(); // Stores query output
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
	elseif($rows = $result->num_rows)  // If rows returned
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_NUM); // Convert it into an associative array
		}
	return $output; // Return the result as a 2D array
}

function ItemExists($conn, $itemID) {
	if(!$conn) return null; // DB connection must be passed in
	$stmt = "SELECT * FROM Item WHERE itemID='$itemID'"; // Query command to execute
	$result = $conn->query($stmt); // Attempt to retrieve record by executing query command
	if(!$result) die(mysql_fatal_error("Database access failed: ".$conn->error));
	/* Execute below if no DB error. DB errors are not printed to the user. */
	$row = $result->fetch_array(MYSQLI_NUM); // Convert it into an associative array
	$result->close(); // Close result
	if($row[0] == '') return false; // If query is empty, user does not exist
	else return true; // User exists
}

/**
 * Method to insert an item in the DB
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @param $picture
 * @return bool
 */
function ItemInsert($conn, $itemID, $title, $price, $weight, $description, $picture) {
	if(!$conn) return null; // DB connection must be passed in
	if($itemID == null || $title == null || $price == null || $weight == null || $description == null || $picture == null)
		return false; // If any field is blank, return false (false = failure)
	$stmt = $conn->prepare("INSERT INTO Item VALUES (?, ?, ?, ?, ?, ?)"); // Variables sanitized using prepared statement
	$stmt->bind_param('ssddss', $itemID, $title, $price, $weight, $description, $picture);
	$output = $stmt->execute(); // Success = TRUE, Fail = FALSE
	$stmt->close(); // Close statement
	return $output; // Return if success or not
}

/**
 * Method to update an item in the DB
 * @param $conn
 * @param $OLD_itemID
 * @param $OLD_title
 * @param $OLD_price
 * @param $OLD_weight
 * @param $OLD_description
 * @param $OLD_picture
 * @param $NEW_itemID
 * @param $NEW_title
 * @param $NEW_price
 * @param $NEW_weight
 * @param $NEW_description
 * @param $NEW_picture
 * @return null
 */
function ItemUpdate($conn, $OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description, $OLD_picture, $NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description, $NEW_picture) {
	if(!$conn) return null; // DB connection must be passed in
	ItemSanitizeVariables($conn, $OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description, $OLD_picture); // Sanitize variables by reference
	ItemSanitizeVariables($conn, $NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description, $NEW_picture); // Sanitize variables by reference
	// SQL update statement
	$stmt = ItemUpdateHelper($NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description, $NEW_picture).ItemAddConditions($OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description, $OLD_picture).";";
	$output = $conn->query($stmt); // Execute statement: Success = TRUE, Fail = FALSE
	$stmt->close(); // Close statement
	return $output; // Return if success or not
}

/**
 * Method to delete an item in the DB
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @param $picture
 * @return null
 */
function ItemDelete($conn, $itemID, $title, $price, $weight, $description, $picture) {
	if(!$conn) return null; // DB connection must be passed in
	// Sanitize variables by reference
	ItemSanitizeVariables($conn, $itemID, $title, $price, $weight, $description, $picture);
	// Add var conditions and ; to end statement
	$stmt = "DELETE FROM Item".ItemAddConditions($itemID, $title, $price, $weight, $description, $picture).";";
	return $conn->query($stmt); // Execute statement: Success = TRUE, Fail = FALSE
}

/**
 * Method to create and initialize table
 * @param $conn
 * @return null
 */
function ItemInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) // Check connection. If cannot connect to DB, terminate program.
		die(mysql_fatal_error("Could not access DB when building Item table: ".$conn->error));
	$query = "CREATE TABLE IF NOT EXISTS Item ( 
		itemID CHAR(4) NOT NULL, 
		title VARCHAR(50) NOT NULL,
		price DECIMAL(8,2) NOT NULL,
		weight DECIMAL(8,2) NOT NULL,
		description TEXT,
		picture VARCHAR(255) NOT NULL,
		PRIMARY KEY (itemID)
	);";
	if(!$conn->query($query)) // Check if statement was executed. If cannot build table, terminate program.
		die(mysql_fatal_error("Could not build table: ".$conn->error));
	return null;
}

/**
 * Sanitizes variables and determines which variable conditions to add to SQL statement
 * Used to add AND conditions for search and SET for update
 * @param null $itemID
 * @param null $title
 * @param null $price
 * @param null $weight
 * @param null $description
 * @param null $picture
 * @return string
 */
function ItemAddConditions($itemID, $title, $price, $weight, $description, $picture) {
	$stmt = " WHERE 1=1"; // Output
	if($itemID) $stmt .= " AND itemID='$itemID'"; // Specify itemID
	if($title) $stmt .= " AND title='$title'"; // Specify itemID
	if($price) $stmt .= " AND price=$price"; // Specify $price
	if($weight) $stmt .= " AND weight=$weight"; // Specify weight
	if($description) $stmt .= " AND description='$description'"; // Specify description
	if($picture) $stmt .= " AND picture='$picture'"; // Specify picture
	return $stmt; // Return statement with sanitized variable conditions
}

/**
 * Determines which fields to update and returns update statement
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @param $picture
 * @return string
 */
function ItemUpdateHelper($itemID, $title, $price, $weight, $description, $picture) {
	$stmt = "UPDATE Item SET "; // Output
	if($itemID) $stmt .= "itemID='$itemID'"; // Specify itemID
	if($title) $stmt .= ", title='$title'"; // Specify itemID
	if($price) $stmt .= ", price=$price"; // Specify $price
	if($weight) $stmt .= ", weight=$weight"; // Specify weight
	if($description) $stmt .= ", description='$description'"; // Specify description
	if($picture) $stmt .= ", picture='$picture'"; // Specify picture
	return $stmt; // Return statement with sanitized variable conditions
}

/**
 * Sanitize variables by reference
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @param $picture
 * @return null
 */
function ItemSanitizeVariables($conn, &$itemID, &$title, &$price, &$weight, &$description, &$picture) {
	if(!$conn) return null; // DB connection must be passed in
	// Sanitize variables by reference - if we are not using prepared statement
	$itemID = sanitizeMySQL($conn, $itemID);
	$title = sanitizeMySQL($conn, $title);
	$price = sanitizeMySQL($conn, $price);
	$weight = sanitizeMySQL($conn, $weight);
	$description = sanitizeMySQL($conn, $description);
	$picture = sanitizeMySQL($conn, $picture);
	return null;
}