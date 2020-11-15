<?php
require_once 'DatabaseMethods.php'; // Load methods for error and sanitization
require_once 'ItemMethods.php'; // Load methods Item table
/**
 * ###############################################################################
 * ########## Table: Inventory(itemID, warehouse, quantity, update)     ##########
 * ########## itemID: CHAR(4) # Store a 4 digit itemID                  ##########
 * ########## warehouse: CHAR(1) # Store letter of warehouse            ##########
 * ########## quantity: SMALLINT # Max: unsigned 32767, signed 65535    ##########
 * ########## update: TIMESTAMP # Timestamp of last update              ##########
 * ###############################################################################
 */

/**
 * Method to create and initialize table
 * @param $conn
 * @return null
 */
function InventoryInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Item table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Inventory ( 
		itemID CHAR(4) NOT NULL, 
		warehouse CHAR(1) NOT NULL,
		quantity SMALLINT NOT NULL DEFAULT 0,
		last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (itemID, warehouse)
	);";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Check if statement was executed. If cannot build table, terminate program.
}

function InventoryInsert($conn, $itemID, $warehouse, $quantity, $last_update) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($itemID) || is_null($warehouse)) return false; // If blank fields: $itemID and $warehouse cannot be null
	if(InventoryExists($conn, $itemID, $warehouse) != 0) return false; // If item already exists, don't insert
	if($stmt = $conn->prepare("INSERT INTO Inventory VALUES (?, ?, ?, ?)")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ssds', $itemID, $warehouse, $quantity, $last_update); // Bind params for sanitization
		if(!$stmt->execute()) // Execute statement: Success = TRUE, Failure = FALSE
			die(mysql_fatal_error("Could not insert: ".$conn->error)); // Error with prepare statement
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

/*function InventoryUpdateByItemID($conn, $OLD_itemID, $itemID, $warehouse, $quantity, $last_update) {
	if(!$conn) return null; // DB connection must be passed in
	if(InventoryExists($conn, $OLD_itemID) == 0) return false; // If item with old ID doesn't exists, return false (no item to update)
	if(InventoryExists($conn, $itemID) == 1) return false; // If item with new ID already exists, return false (prevent conflict with existing items)
	if($stmt = $conn->prepare("Update Inventory SET itemID=?, title=?, price=?, weight=?, description=? WHERE itemID=?")) { // Placeholders for further sanitization
		$stmt->bind_param('ssds', $itemID, $warehouse, $quantity, $last_update); // Bind params for sanitization
		$stmt->execute(); // Execute query
		$affected_rows = $stmt->affected_rows; // Store number of rows affected
		$stmt->close();
		return $affected_rows;
	} else die(mysql_fatal_error("Could not build table: ".$conn->error)); // Error with prepare statement
}*/

function InventoryUpdateQuantity($conn, $itemID, $warehouse, $quantity) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("Update Inventory SET quantity=? WHERE itemID=? AND warehouse=?")) { // Placeholders for further sanitization
		$stmt->bind_param('dss', $quantity, $itemID, $warehouse); // Bind params for sanitization
		$stmt->execute(); // Execute query
		$affected_rows = $stmt->affected_rows; // Store number of rows affected
		$stmt->close();
		return $affected_rows;
	} else die(mysql_fatal_error("Could not build table: ".$conn->error)); // Error with prepare statement

}

function InventoryUpdateItemID($conn, $OLD_itemID, $itemID) {
	if(!$conn) return null; // DB connection must be passed in
	//if(InventoryExists($conn, $OLD_itemID) == 0) return false; // If item with old ID doesn't exists, return false (no item to update)
	if(InventoryExists($conn, $itemID, 'A') == 1 && InventoryExists($conn, $itemID, 'B') == 1) return false; // If item with new ID already exists, return false (prevent conflict with existing items)
	if($stmt = $conn->prepare("Update Inventory SET itemID=? WHERE itemID=?")) { // Placeholders for further sanitization
		$stmt->bind_param('ss', $itemID, $OLD_itemID); // Bind params for sanitization
		$stmt->execute(); // Execute query
		$affected_rows = $stmt->affected_rows; // Store number of rows affected
		$stmt->close();
		return $affected_rows;
	} else die(mysql_fatal_error("Could not update table: ".$conn->error)); // Error with prepare statement
}

function InventoryUpdate($conn, $OLD_itemID, $OLD_warehouse, $OLD_quantity, $OLD_last_update,
                         $NEW_itemID, $NEW_warehouse, $NEW_quantity, $NEW_last_update) {
	if(!$conn) return null; // DB connection must be passed in
	InventorySanitizeVariables($conn, $OLD_itemID, $OLD_warehouse, $OLD_quantity, $OLD_last_update); // Sanitize variables by reference
	InventorySanitizeVariables($conn, $NEW_itemID, $NEW_warehouse, $NEW_quantity, $NEW_last_update); // Sanitize variables by reference
	// SQL update statement
	$stmt = InventoryUpdateHelper($NEW_itemID, $NEW_warehouse, $NEW_quantity, $NEW_last_update).ItemAddConditions($OLD_itemID, $OLD_warehouse, $OLD_quantity, $OLD_last_update).";";
	$output = $conn->query($stmt); // Execute statement: Success = TRUE, Fail = FALSE
	//$stmt->close(); // Close statement
	return $output; // Return if success or not
}


function InventoryUpdateHelper($itemID, $warehouse, $quantity, $last_update) {
	$stmt = "UPDATE Item SET"; // Output
	if($itemID) $stmt .= " itemID='$itemID'"; // Specify itemID
	if($warehouse) $stmt .= ", warehouse='$warehouse'";
	if($quantity) $stmt .= ", quantity=$quantity";
	if($last_update) $stmt .= ", weight=$last_update";
	return $stmt; // Return statement with sanitized variable conditions
}


function InventorySearch($conn, $itemID, $warehouse, $quantity, $last_update) {
	if(!$conn) return null; // DB connection must be passed in
	InventorySanitizeVariables($conn, $itemID, $warehouse, $quantity, $last_update);// Sanitize variables by reference
	$stmt = "SELECT * FROM Inventory".InventoryAddConditions($itemID, $warehouse, $quantity, $last_update).";"; // Add var conditions and ;
	$result = $conn->query($stmt); // Execute query statement
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

function InventorySearchByItemID($conn, $itemID, $warehouse) {
	if(is_null($conn) || is_null($itemID) || is_null($warehouse)) return;
	if($stmt = $conn->prepare("SELECT * FROM Inventory WHERE itemID=? AND warehouse=?;")) { // Placeholders for further sanitization
		$stmt->bind_param("ss", $itemID, $warehouse); /* bind parameters for markers */
		$stmt->execute(); /* execute query */
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row;
	}
	return null;
}


function InventoryDelete($conn, $itemID, $warehouse, $quantity, $last_update) {
	if(!$conn) return null; // DB connection must be passed in
	InventorySanitizeVariables($conn, $itemID, $warehouse, $quantity, $last_update);// Sanitize variables by reference
	$stmt = "DELETE FROM Inventory".InventoryAddConditions($itemID, $warehouse, $quantity, $last_update).";"; // Add var conditions and ;
	$output = $conn->query($stmt); // Execute statement: Success = TRUE, Fail = FALSE
	//$stmt->close(); // Close statement for security
	return $output; // Return true if successful, false if failure
}

function InventoryExists($conn, $itemID, $warehouse) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Inventory WHERE itemID=? AND warehouse=?;")) {
		$stmt->bind_param("ss", $itemID, $warehouse); /* bind parameters for markers */
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
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