<?php
require_once 'DatabaseSecurityMethods.php'; // Load methods for error and sanitization
require_once 'InventoryMethods.php'; // Load methods for Inventory table

/**
 * ###############################################################################
 * ########## Table: Item(itemID, title, price, weight, description)    ##########
 * ########## itemID: CHAR(4) # Store a 4 digit ID                      ##########
 * ########## title: VARCHAR(50) # Store a 50 character or less title   ##########
 * ########## price: DECIMAL(8,2) # Max price: 999,999.99               ##########
 * ########## weight: DECIMAL(8,2) # Max weight: 999,999.99             ##########
 * ########## description: VARCHAR(1000) # Store description paragraphs ##########
 * ###############################################################################
 */

/**
 * @param $conn
 * @param $itemID
 * @return null
 */
function ItemSearchByItemID($conn, $itemID) {
	if($stmt = $conn->prepare("SELECT * FROM Item WHERE itemID=?;")) { // Placeholders for further sanitization
		$stmt->bind_param("s", $itemID); /* bind parameters for markers */
		$stmt->execute(); /* execute query */
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row;
	}
	return null;
}

/**
 * Method to update an item in the DB
 * @param $conn
 * @param $OLD_itemID
 * @param $OLD_title
 * @param $OLD_price
 * @param $OLD_weight
 * @param $OLD_description
 * @param $NEW_itemID
 * @param $NEW_title
 * @param $NEW_price
 * @param $NEW_weight
 * @param $NEW_description
 * @return null
 */
function ItemUpdate($conn, $OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description, $NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description) {
	if(!$conn) return null; // DB connection must be passed in
	ItemSanitizeVariables($conn, $OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description); // Sanitize variables by reference
	ItemSanitizeVariables($conn, $NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description); // Sanitize variables by reference
	// SQL update statement
	$stmt = ItemUpdateHelper($NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description).ItemAddConditions($OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description).";";
	$output = $conn->query($stmt); // Execute statement: Success = TRUE, Fail = FALSE
	//$stmt->close(); // Close statement
	InventoryUpdate($conn, $OLD_itemID, null, null, null, $NEW_itemID, null, null, null);
	return $output; // Return if success or not
}

/**
 * Determines which fields to update and returns update statement
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return string
 */
function ItemUpdateHelper($itemID, $title, $price, $weight, $description) {
	$stmt = "UPDATE Item SET"; // Output
	if($itemID) $stmt .= " itemID='$itemID'"; // Specify itemID
	if($title) $stmt .= ", title='$title'"; // Specify itemID
	if($price) $stmt .= ", price=$price"; // Specify $price
	if($weight) $stmt .= ", weight=$weight"; // Specify weight
	if($description) $stmt .= ", description='$description'"; // Specify description
	return $stmt; // Return statement with sanitized variable conditions
}

/** ################################################################################### */
/** ############################## Working Methods Below ############################## */
/** ################################################################################### */

/**
 * Method to create and initialize table
 * @param $conn
 * @return null
 */
function ItemInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Item table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Item ( 
		itemID CHAR(4) NOT NULL, 
		title VARCHAR(50) NOT NULL,
		price DECIMAL(8,2) NOT NULL,
		weight DECIMAL(8,2) NOT NULL,
		description VARCHAR(1000),
		PRIMARY KEY (itemID)
	);";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Check if statement was executed. If cannot build table, terminate program.
}

/**
 * Method to insert an item in the DB.
 * Checks if item exists and inserts if item DNE, insert.
 * Returns true if successful insert, else false for failure.
 * The field "description" can be null (as specified in the table).
 * All other fields must not be null.
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return bool
 */
function ItemInsert($conn, $itemID, $title, $price, $weight, $description) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($itemID) || is_null($title) || is_null($price) || is_null($weight)) return false; // If blank fields
	if(ItemExists($conn, $itemID) != 0) return false; // If item already exists
	if($stmt = $conn->prepare("INSERT INTO Item VALUES (?, ?, ?, ?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ssdds', $itemID, $title, $price, $weight, $description); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		InventoryInsert($conn, $itemID, 'A', 0, null); // Create inventory object for warehouse A
		InventoryInsert($conn, $itemID, 'B', 0, null); // Create inventory object for warehouse B
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

/**
 * Method to delete an item in the DB
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return null
 */
function ItemDelete($conn, $itemID, $title, $price, $weight, $description) {
	if(!$conn) return null; // DB connection must be passed in
	ItemSanitizeVariables($conn, $itemID, $title, $price, $weight, $description); // Sanitize variables by reference
	$stmt = "DELETE FROM Item".ItemAddConditions($itemID, $title, $price, $weight, $description).";"; // Add var conditions and ;
	$output = $conn->query($stmt); // Execute statement: Success = TRUE, Fail = FALSE
	//$stmt->close(); // Close statement for security
	InventoryDelete($conn, $itemID, null, null, null); // Delete all inventories for this item
	return $output; // Return true if successful, false if failure
}

function ItemExists($conn, $itemID) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Item WHERE itemID=?;")) {
		$stmt->bind_param("s", $itemID); /* bind parameters for markers */
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}


/**
 * Update an existing item by using itemID
 * Use prepared statement for sanitization
 * @param $conn
 * @param $OLD_itemID
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return false|null
 */
function ItemUpdateByItemID($conn, $OLD_itemID, $itemID, $title, $price, $weight, $description) {
	if(!$conn) return null; // DB connection must be passed in
	//if(ItemExists($conn, $OLD_itemID) == 0) return false; // If item with old ID doesn't exists, return false (no item to update)
	//if(ItemExists($conn, $itemID) == 1) return false; // If item with new ID already exists, return false (prevent conflict with existing items)
	if($stmt = $conn->prepare("Update Item SET itemID=?, title=?, price=?, weight=?, description=? WHERE itemID=?")) { // Placeholders for further sanitization
		$stmt->bind_param("ssddss", $itemID, $title, $price, $weight, $description, $OLD_itemID); /* bind parameters for markers */
		$stmt->execute(); // Execute query
		$affected_rows = $stmt->affected_rows; // Store number of rows affected
		//$stmt->close();
		InventoryUpdateItemID($conn, $OLD_itemID, $itemID);
		return $affected_rows;
	} else die(mysql_fatal_error("Could not build table: ".$conn->error)); // Error with prepare statement
}


/**
 * Method to search/query an item from the DB
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return null
 */
function ItemSearch($conn, $itemID, $title, $price, $weight, $description) {
	if(!$conn) return null; // DB connection must be passed in
	ItemSanitizeVariables($conn, $itemID, $title, $price, $weight, $description); // Sanitize variables by reference
	$stmt = "SELECT * FROM Item".ItemAddConditions($itemID, $title, $price, $weight, $description).";"; // Add var conditions and ;
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

/**
 * Sanitizes variables and determines which variable conditions to add to SQL statement
 * Used to add AND conditions for search and SET for update
 * @param null $itemID
 * @param null $title
 * @param null $price
 * @param null $weight
 * @param null $description
 * @return string
 */
function ItemAddConditions($itemID, $title, $price, $weight, $description) {
	$stmt = " WHERE 1=1"; // Output
	if($itemID) $stmt .= " AND itemID='$itemID'"; // Specify itemID
	if($title) $stmt .= " AND title='$title'"; // Specify itemID
	if($price) $stmt .= " AND price=$price"; // Specify $price
	if($weight) $stmt .= " AND weight=$weight"; // Specify weight
	if($description) $stmt .= " AND description='$description'"; // Specify description
	return $stmt; // Return statement with sanitized variable conditions
}

/**
 * Sanitize variables by reference
 * Useful if we are not using prepared statement to sanitize variables
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return bool
 */
function ItemSanitizeVariables($conn, &$itemID, &$title, &$price, &$weight, &$description) {
	if(!$conn) return false; // DB connection must be passed in
	$itemID = sanitizeMySQL($conn, $itemID);
	$title = sanitizeMySQL($conn, $title);
	$price = sanitizeMySQL($conn, $price);
	$weight = sanitizeMySQL($conn, $weight);
	$description = sanitizeMySQL($conn, $description);
	return true; // Return true if variables were successfully sanitized
}