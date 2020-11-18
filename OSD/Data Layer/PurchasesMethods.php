<?php
/**
 * ###################################################################################
 * ########## Purchases(orderID, itemID, item_price, multiplicity)          ##########
 * ########## orderID: CHAR(8) # Store a 8 char order ID                    ##########
 * ########## itemID: CHAR(4) # Store a 4 digit user ID                     ##########
 * ########## price: DECIMAL(8,2) # Max price: 999,999.99                   ##########
 * ########## multiplicity: SMALLINT # Store a multiplicity of item in cart ##########
 * ###################################################################################
 */

function PurchasesInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	/* Check connection. If cannot connect to DB, terminate program. */
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Cart table: ".$conn->error));
	/* Query string to create Orders table */
	$query = "CREATE TABLE IF NOT EXISTS Purchases ( 
		orderID CHAR(8) NOT NULL,
		itemID CHAR(4) NOT NULL,
		price DECIMAL(8,2) NOT NULL,
		multiplicity SMALLINT NOT NULL,
		PRIMARY KEY (orderID, itemID)
	);";
	/* Check if statement was executed. If cannot build table, terminate program. */
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error));
}

function PurchasesInsert($conn, $orderID, $itemID, $price, $multiplicity) {
	/* Params cannot be null */
	if(is_null($conn) || is_null($orderID) || is_null($itemID) || is_null($price) || is_null($multiplicity)) return false; // Insert failed
	if(PurchasesExists($conn, $orderID, $itemID) != 0) return false; // If purchase already exists (with same orderID)
	/* Use prepared statement to insert for further sanitization */
	if($stmt = $conn->prepare("INSERT INTO Purchases VALUES (?, ?, ?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ssdd', $orderID, $itemID, $price, $multiplicity); // Bind params for sanitization
		$stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}


function PurchasesDelete($conn, $orderID) {
	if(is_null($conn) || is_null($orderID)) return null; // Params cannot be null
	if($stmt = $conn->prepare("DELETE FROM Purchases WHERE orderID=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $orderID); // Bind params for sanitization
		/* Execute statement: Success = TRUE, Failure = FALSE */
		if(!$stmt->execute()) die(mysql_fatal_error("Could not delete: ".$conn->error)); // Error with prepare statement
		$stmt->close(); // Close statement
		return true; // Return if successful delete
	}
	return false; // If prepared statement failed, return false
}


function PurchasesSearch($conn, $orderID) {
	if(is_null($conn) || is_null($orderID)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Purchases WHERE orderID=?;")) { // Placeholders for further sanitization
		$stmt->bind_param("s", $orderID); // bind parameters for markers
		$stmt->execute(); // execute query
		$result = $stmt->get_result();
		/* Store result of rows in a 2D array and return */
		$output[] = array(); // 2D array to store query output (array of rows)
		if(is_null($result)) die(mysql_fatal_error($conn->error)); // Error with query
		elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
			for($i = 0; $i < $rows; $i++) { // Store all entries in table
				$result->data_seek($i); // Get the i^th row
				$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
			}
		// If no rows returned, don't store anything
		$result->close(); // Close statement for security
		$stmt->close(); // Close statement for security
		return $output; // Return the result as a 2D array
	}
	return null;
}

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