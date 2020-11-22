<?php
/**
 * #######################################################################################
 * ########## Purchases(orderID, itemID, item_price, multiplicity)              ##########
 * ########## orderID: CHAR(8)          # Store a 8 char order ID               ##########
 * ########## itemID: CHAR(4)           # Store a 4 digit user ID               ##########
 * ########## price: DECIMAL(8,2)       # Max price: 999,999.99                 ##########
 * ########## multiplicity: SMALLINT    # Store a multiplicity of item in cart  ##########
 * #######################################################################################
 */

function InitializePurchases($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Purchases ( 
		orderID CHAR(8) NOT NULL,
		itemID CHAR(4) NOT NULL,
		price DECIMAL(8,2) NOT NULL,
		multiplicity SMALLINT NOT NULL,
		PRIMARY KEY (orderID, itemID)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

function InsertPurchase($conn, $orderID, $itemID, $price, $multiplicity) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID type cannot be null");
	if(!$itemID) mysql_fatal_error("Item ID type cannot be null");
	if(!$price) mysql_fatal_error("Price cannot be null");
	if(!$multiplicity) mysql_fatal_error("Multiplicity cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Purchases VALUES (?, ?, ?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ssdd', $orderID, $itemID, $price, $multiplicity)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}


function DeletePurchase($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID type cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Purchases WHERE orderID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function SearchPurchase($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID type cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Purchases WHERE orderID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
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

function PurchasesExists($conn, $orderID, $itemID) {
	if(is_null($conn) || is_null($orderID) || is_null($itemID)) return -1; // Params cannot be null
	if($stmt = $conn->prepare("SELECT * FROM Purchases WHERE orderID=? AND itemID=?;")) {
		$stmt->bind_param("ss", $orderID, $itemID); // bind parameters for markers
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
	return -1; // Error with prepared statement
}