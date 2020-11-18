<?php
require_once 'DatabaseMethods.php';
/**
 * ###############################################################################################################
 * ########## Orders(orderID, email, order_total, order_weight, shipping_option, address, date_placed)  ##########
 * ########## orderID: CHAR(8) # Store a 8 char order ID                                                ##########
 * ########## email: VARCHAR(50) # Store a 50 char email                                                ##########
 * ########## order_weight: SMALLINT # Store order weight < 32,767                                      ##########
 * ########## shipping_option: VARCHAR(8) # Store a 8 char order option                                 ##########
 * ########## address: VARCHAR(100) # Store a max 100 char address                                      ##########
 * ########## date_placed: TIMESTAMP # Timestamp of order placement                                     ##########
 * ###############################################################################################################
 */

function OrdersInitialize($conn) {
	if(is_null($conn)) return null; // DB connection must be passed in
	/* Check connection. If cannot connect to DB, terminate program. */
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Order table: ".$conn->error));
	/* Query string to create Orders table */
	$query = "CREATE TABLE IF NOT EXISTS Orders ( 
		orderID CHAR(8) NOT NULL,
		email VARCHAR(50) NOT NULL,
		order_weight SMALLINT NOT NULL,
		shipping_option VARCHAR(8) NOT NULL,
		address VARCHAR(100) NOT NULL,
		date_placed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (orderID)
	);";
	/* Check if statement was executed. If cannot build table, terminate program. */
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error));
}

function OrdersInsert($conn, $orderID, $email, $order_weight, $shipping_option, $address) {
	/* Params cannot be null */
	if(is_null($conn) || is_null($orderID) || is_null($email)|| is_null($order_weight)
		|| is_null($shipping_option) || is_null($address)) return false; // Insert failed
	/* Use prepared statement to insert for further sanitization */
	if($stmt = $conn->prepare("INSERT INTO Orders VALUES (?, ?, ?, ?, ?, NULL);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ssdss', $orderID, $email, $order_weight, $shipping_option, $address); // Bind params for sanitization
		/* Execute statement: Success = TRUE, Failure = FALSE */
		if(!$stmt->execute()) die(mysql_fatal_error("Could not insert: ".$conn->error)); // Error with prepare statement
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function OrdersDelete($conn, $orderID) {
	if(is_null($conn) || is_null($orderID)) return null; // Params cannot be null
	if($stmt = $conn->prepare("DELETE FROM Orders WHERE orderID=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $orderID); // Bind params for sanitization
		/* Execute statement: Success = TRUE, Failure = FALSE */
		if(!$stmt->execute()) die(mysql_fatal_error("Could not delete: ".$conn->error)); // Error with prepare statement
		$stmt->close(); // Close statement
		return true; // Return if successful delete
	}
	return false; // If prepared statement failed, return false
}

function OrdersSearchByOrdersID($conn, $orderID) {
	if(is_null($conn)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Orders WHERE orderID=? ORDER BY date_placed DESC;")) { // Placeholders for further sanitization
		$stmt->bind_param("s", $orderID); // bind parameters for markers
		$stmt->execute(); // execute query
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row;
	}
	return null;
}

function OrdersSearchByEmail($conn, $email) {
	if(is_null($conn)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Orders WHERE email=? ORDER BY date_placed DESC;")) { // Placeholders for further sanitization
		$stmt->bind_param("s", $email); // bind parameters for markers
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

function OrderExists($conn, $orderID) {
	if(is_null($conn) || is_null($orderID)) return -1; // Params cannot be null
	if($stmt = $conn->prepare("SELECT * FROM Orders WHERE orderID=?;")) {
		$stmt->bind_param("s", $orderID); // bind parameters for markers
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
	return -1; // Error with prepared statement
}

function GenerateOrderID($conn) {
	$digits = 8; // Generate an 8-digit string
	$charSet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Character set
	$output = ''; // Random string
	for($i = 0; $i < $digits; $i++) { // Iterate for each digit
		$output .= $charSet[rand(0, strlen($charSet) - 1)]; // Get a random index from charSet, retrieve the char, append it
	}
	/* Check if orderID is unique */
	$exists = OrderExists($conn, $output);
	if($exists > 0) return generateOrderID($conn); // OrderID exists: Regenerate orderID
	if($exists === -1) return null; // DB error
	return $output; // Return unique orderID
}
