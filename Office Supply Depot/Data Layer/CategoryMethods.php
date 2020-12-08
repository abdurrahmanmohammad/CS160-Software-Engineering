<?php
require_once 'DatabaseMethods.php';

/**
 * #######################################################################################
 * ########## Categories(itemID, category)                                      ##########
 * ########## itemID: CHAR(4)               # Store a 4 digit ID                ##########
 * ########## category: VARCHAR(20)         # Store a category (max 20 chars)   ##########
 * #######################################################################################
 */

function InitializeCategories($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Categories ( 
		itemID CHAR(4) NOT NULL, 
		category VARCHAR(20) NOT NULL,
		PRIMARY KEY (itemID, category)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}


function InsertCategory($conn, $itemID, $category) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$category) mysql_fatal_error("Category cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Categories VALUES (?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $category)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

function DeleteCategory($conn, $itemID, $category) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$category) mysql_fatal_error("Category cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Categories WHERE itemID=? AND category=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $category)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function DeleteCategoryByItemID($conn, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Categories WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}


function SearchCategories($conn, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID type cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Categories WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get result */
	$result = $stmt->get_result(); // Get query result
	if($result->num_rows == 0) return null; // If result is empty, return null
	/** Store each row of result in output */
	$output[] = array(); // 2D array to store query output (array of rows)
	if(!$result) mysql_fatal_error($conn->error); // Error: execute custom error function
	if($result->num_rows == 0) return null; // If result is empty, return null
	elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		}
	$stmt->close(); // Close statement
	return $output; // Return true after successful delete
}

function GetAllCategories($conn) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Perform search */
	$result = $conn->query("SELECT DISTINCT category FROM Categories;"); // Execute query statement
	$output[] = array(); // 2D array to store query output (array of rows)
	if(!$result) mysql_fatal_error($conn->error); // Error: execute custom error function
	if($result->num_rows == 0) return null; // If result is empty, return null
	elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		}
	$result->close(); // Close statement for security
	return $output; // Return the result as a 2D array
}

function GetItemsByCategories($conn, $category) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$category) mysql_fatal_error("Category cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Items NATURAL JOIN Categories WHERE category=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $category)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
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
	return $output; // Return true after successful delete
}

/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

/**
 * Checks if an category for an item exists
 * Update: Use the search method instead
 * @param $conn
 * @param $itemID
 * @param $category
 * @return mixed
 */
function CategoryExists($conn, $itemID, $category) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$category) mysql_fatal_error("Category cannot be null");
	/** Perform query */
	if(!$stmt = $conn->prepare("SELECT * FROM Categories WHERE itemID=? AND category=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $category)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	if(!$stmt->store_result()) mysql_fatal_error("Store result failed : ".$conn->error); // Store result
	$rowcount = $stmt->num_rows; // Get rowcount
	$stmt->close(); // Close statement
	return $rowcount; // Return true after successful inserts
}