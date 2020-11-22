<?php
require_once 'DatabaseMethods.php'; // Load methods for error and sanitization
require_once 'InventoryMethods.php'; // Load methods for Inventory table
require_once 'CategoryMethods.php';
require_once 'PictureMethods.php';

/**
 * ###############################################################################
 * ########## Table: Items(itemID, title, price, weight, description)    ##########
 * ########## itemID: CHAR(4) # Store a 4 digit ID                      ##########
 * ########## title: VARCHAR(50) # Store a 50 character or less title   ##########
 * ########## price: DECIMAL(8,2) # Max price: 999,999.99               ##########
 * ########## weight: DECIMAL(8,2) # Max weight: 999,999.99             ##########
 * ########## description: VARCHAR(1000) # Store description paragraphs ##########
 * ###############################################################################
 */

/**
 * Method to create and initialize table
 * @param $conn
 * @return null
 */
function InitializeItems($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Items ( 
		itemID CHAR(4) NOT NULL, 
		title VARCHAR(50) NOT NULL,
		price DECIMAL(8,2) NOT NULL,
		weight DECIMAL(8,2) NOT NULL,
		description VARCHAR(1000),
		PRIMARY KEY (itemID)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

/**
 * Method to insert an Items in the DB.
 * Checks if Items exists and inserts if Items DNE, insert.
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
function InsertItem($conn, $itemID, $title, $price, $weight, $description) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$title) mysql_fatal_error("Title cannot be null");
	if(!$price) mysql_fatal_error("Price cannot be null");
	if(!$weight) mysql_fatal_error("Weight cannot be null");
	if(!$description) mysql_fatal_error("Description cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Items VALUES (?, ?, ?, ?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ssdds', $itemID, $title, $price, $weight, $description)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

/**
 * Method to delete an Items in the DB
 */
function DeleteItem($conn, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Items WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	/** Delete all inventories for this item */
	DeleteInventoryByItemID($conn, $itemID);
	/** Delete all categories associated with this item */
	DeleteCategoryByItemID($conn, $itemID);
	/** Delete all the pictures associated with this item */
	$pictures = SearchPicture($conn, $itemID); // Get all pictures for an item
	foreach($pictures as $picture) DeletePicture($conn, $itemID, $picture['directory']); // Delete the picture from the DB and server
	return true; // Return true after successful delete
}

/**
 * Update an existing Items by using itemID
 * Use prepared statement for sanitization
 */
function UpdateItem($conn, $itemID, $title, $price, $weight, $description) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	if(!$title) mysql_fatal_error("Title cannot be null");
	if(!$price) mysql_fatal_error("Price cannot be null");
	if(!$weight) mysql_fatal_error("Weight cannot be null");
	if(!$description) mysql_fatal_error("Description cannot be null");
	/** Update: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("Update Items SET title=?, price=?, weight=?, description=? WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('sddss', $title, $price, $weight, $description, $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$affected_rows = $stmt->affected_rows; // Store number of rows affected
	$stmt->close(); // Close statement
	return $affected_rows; // Return number of rows affected by update
}

function SearchItem($conn, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("Item ID cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Items WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get search result */
	$result = $stmt->get_result(); // Get query result
	if($result->num_rows == 0) return null; // If result is empty, return null
	$output = $result->fetch_array(MYSQLI_BOTH); // Convert result into an associative array
	$stmt->close(); // Close statement
	return $output; // Return output
}

function GetAllItems($conn) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Search: sanitize variables with prepared statement */
	$sql = "SELECT * FROM Items;";
	$result = mysqli_query($conn, $sql);
	/** Store each row of result in output */
	$output[] = array(); // 2D array to store query output (array of rows)
	if(!$result) mysql_fatal_error($conn->error); // Error: execute custom error function
	elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		}
	$result->close(); // Close statement
	return $output; // Return true after successful delete
}


/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

function ItemExists($conn, $itemID) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Items WHERE itemID=?;")) {
		$stmt->bind_param("s", $itemID); /* bind parameters for markers */
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}

/**
 * Method to update an Items in the DB
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
function ItemUpdateAll($conn, $OLD_itemID, $OLD_title, $OLD_price, $OLD_weight, $OLD_description, $NEW_itemID, $NEW_title, $NEW_price, $NEW_weight, $NEW_description) {
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
	$stmt = "UPDATE Items SET"; // Output
	if($itemID) $stmt .= " itemID='$itemID'"; // Specify itemID
	if($title) $stmt .= ", title='$title'"; // Specify itemID
	if($price) $stmt .= ", price=$price"; // Specify $price
	if($weight) $stmt .= ", weight=$weight"; // Specify weight
	if($description) $stmt .= ", description='$description'"; // Specify description
	return $stmt; // Return statement with sanitized variable conditions
}

/**
 * Method to search/query an Items from the DB
 * @param $conn
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 * @return null
 */
function ItemSearchAll($conn, $itemID, $title, $price, $weight, $description) {
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