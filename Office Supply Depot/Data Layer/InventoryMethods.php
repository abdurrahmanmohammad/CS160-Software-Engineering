<?php
require_once 'DatabaseMethods.php'; // Load methods for error and sanitization
require_once 'ItemMethods.php'; // Load methods Item table
/**
 * ###########################################################################################
 * ########## Table: Inventory(itemID, warehouse, quantity, update)                 ##########
 * ########## itemID: CHAR(4)       # Store a 4 digit itemID                        ##########
 * ########## warehouse: CHAR(1)    # Store letter of warehouse                     ##########
 * ########## quantity: SMALLINT    # Max: unsigned 32767, signed 65535             ##########
 * ########## update: TIMESTAMP     # Timestamp of last update (auto generated)     ##########
 * ###########################################################################################
 */

/**
 * Method to create and initialize table
 * @param $conn
 * @return null
 */
function InitializeInventory($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Inventory ( 
		itemID CHAR(4) NOT NULL, 
		warehouse CHAR(1) NOT NULL,
		quantity SMALLINT NOT NULL DEFAULT 0,
		last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (itemID, warehouse)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

/**
 * Inserts inventory information of an item of a particular warehouse
 * Assertion: inventory should not already exist in table
 * @param $conn
 * @param $itemID
 * @param $warehouse
 * @param $quantity
 * @return bool
 */
function InsertInventory($conn, $itemID, $warehouse, $quantity) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$warehouse) mysql_fatal_error("Warehouse cannot be null");
	if(!$quantity) mysql_fatal_error("Quantity cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Inventory VALUES (?, ?, ?, NULL);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ssd', $itemID, $warehouse, $quantity)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

function DeleteInventory($conn, $itemID, $warehouse) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$warehouse) mysql_fatal_error("Warehouse cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Inventory WHERE itemID=? AND warehouse=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $warehouse)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function DeleteInventoryByItemID($conn, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Inventory WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function UpdateInventoryByItemID($conn, $itemID, $warehouse, $quantity) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$warehouse) mysql_fatal_error("Warehouse cannot be null");
	if(!$quantity) mysql_fatal_error("Quantity cannot be null");
	/** Update: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("Update Inventory SET warehouse=? AND quantity=? WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('sds', $warehouse, $quantity, $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$affected_rows = $stmt->affected_rows; // Store number of rows affected
	$stmt->close(); // Close statement
	return $affected_rows; // Return number of rows affected by update
}

function UpdateInventoryQuantity($conn, $itemID, $warehouse, $quantity) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$warehouse) mysql_fatal_error("Warehouse cannot be null");
	//if(!$quantity) mysql_fatal_error("Quantity cannot be null");
	/** Update: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("Update Inventory SET quantity=? WHERE itemID=? AND warehouse=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('dss', $quantity, $itemID, $warehouse)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$affected_rows = $stmt->affected_rows; // Store number of rows affected
	$stmt->close(); // Close statement
	return $affected_rows; // Return number of rows affected by update
}

function SearchInventory($conn, $itemID, $warehouse) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$warehouse) mysql_fatal_error("Warehouse cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Inventory WHERE itemID=? AND warehouse=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $warehouse)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get search result */
	$result = $stmt->get_result(); // Get query result
	if($result->num_rows == 0) return null; // If result is empty, return null
	$output = $result->fetch_array(MYSQLI_BOTH); // Convert result into an associative array
	$stmt->close(); // Close statement
	return $output; // Return output
}

/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

/**
 * Checks if a certain inventory exists
 * Update: Use the search method instead
 * @param $conn
 * @param $itemID
 * @param $warehouse
 * @return mixed
 */
function InventoryExists($conn, $itemID, $warehouse) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$warehouse) mysql_fatal_error("Warehouse cannot be null");
	/** Perform search */
	if(!$stmt = $conn->prepare("SELECT * FROM Inventory WHERE itemID=? AND warehouse=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $warehouse)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	if(!$stmt->store_result()) mysql_fatal_error("Store result failed : ".$conn->error); // Store result
	$rowcount = $stmt->num_rows; // Get rowcount
	$stmt->close(); // Close statement
	return $rowcount; // Return number of rows affected by update
}

/**
 * Sanitizes variables and determines which variable conditions to add to SQL statement
 * Used to add AND conditions for search and SET for update
 * @param $itemID
 * @param $warehouse
 * @param $quantity
 * @param $last_update
 * @return string
 */
function InventoryAddConditions($itemID, $warehouse, $quantity, $last_update) {
	$stmt = " WHERE 1=1"; // Output
	if($itemID) $stmt .= " AND itemID='$itemID'"; // Specify itemID
	if($warehouse) $stmt .= " AND warehouse='$warehouse'"; // Specify itemID
	if($quantity) $stmt .= " AND quantity=$quantity"; // Specify $price
	if($last_update) $stmt .= " AND last_update=$last_update"; // Specify weight
	return $stmt; // Return statement with sanitized variable conditions
}

/**
 * Sanitize variables by reference
 * Useful if we are not using prepared statement to sanitize variables
 * @param $conn
 * @param $itemID
 * @param $warehouse
 * @param $quantity
 * @param $last_update
 * @return bool
 */
function InventorySanitizeVariables($conn, &$itemID, &$warehouse, &$quantity, &$last_update) {
	if(!$conn) return false; // DB connection must be passed in
	$itemID = sanitizeMySQL($conn, $itemID);
	$warehouse = sanitizeMySQL($conn, $warehouse);
	$quantity = sanitizeMySQL($conn, $quantity);
	if($last_update) $last_update = sanitizeMySQL($conn, $last_update); // If not null, sanitize
	return true; // Return true if variables were successfully sanitized
}