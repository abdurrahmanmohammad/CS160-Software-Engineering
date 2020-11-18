<?php
require_once 'DatabaseMethods.php';
/**
 * ###############################################################################################################
 * ########## Transactions(orderID, order_total, card_holder, credit_card, card_month, card_year, cvc)  ##########
 * ########## orderID: CHAR(8) # Store a 8 char order ID                                                ##########
 * ########## order_total: SMALLINT # Store order total < $32,767                                       ##########
 * ########## card_holder: VARCHAR(50) # Store max 50 chars of card holder's name                       ##########
 * ########## credit_card: VARCHAR(16) # Credit cards are 16 digits                                     ##########
 * ########## card_month: CHAR(2) # Credit card month is 2 chars                                        ##########
 * ########## card_year: CHAR(2) # Credit card year is 2 chars                                          ##########
 * ########## cvc: CHAR(3) # Credit card cvc is 3 chars                                                 ##########
 * ###############################################################################################################
 */

function TransactionsInitialize($conn) {
	if(is_null($conn)) return null; // DB connection must be passed in
	/* Check connection. If cannot connect to DB, terminate program. */
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Transactions table: ".$conn->error));
	/* Query string to create Transactions table */
	$query = "CREATE TABLE IF NOT EXISTS Transactions ( 
		orderID CHAR(8) NOT NULL,
		order_total SMALLINT NOT NULL,
		card_holder VARCHAR(50) NOT NULL,
		credit_card VARCHAR(16) NOT NULL,
		card_month CHAR(2) NOT NULL,
		card_year CHAR(2) NOT NULL,
		cvc CHAR(3) NOT NULL,
		PRIMARY KEY (orderID)
	);";
	/* Check if statement was executed. If cannot build table, terminate program. */
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error));
}

function TransactionsInsert($conn, $orderID, $order_total, $card_holder, $credit_card, $card_month, $card_year, $cvc) {
	/* Params cannot be null */
	if(is_null($conn) || is_null($orderID) || is_null($order_total) || is_null($card_holder) || is_null($credit_card)
		|| is_null($card_month) || is_null($card_year) || is_null($cvc)) return false; // Insert failed
	/* Use prepared statement to insert for further sanitization */
	if($stmt = $conn->prepare("INSERT INTO Transactions VALUES (?, ?, ?, ?, ?, ?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('sdsssss', $orderID, $order_total, $card_holder, $credit_card, $card_month, $card_year, $cvc); // Bind params for sanitization
		/* Execute statement: Success = TRUE, Failure = FALSE */
		if(!$stmt->execute()) die(mysql_fatal_error("Could not insert: ".$conn->error)); // Error with prepare statement
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function TransactionsDelete($conn, $orderID) {
	if(is_null($conn) || is_null($orderID)) return null; // Params cannot be null
	if($stmt = $conn->prepare("DELETE FROM Transactions WHERE orderID=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('s', $orderID); // Bind params for sanitization
		/* Execute statement: Success = TRUE, Failure = FALSE */
		if(!$stmt->execute()) die(mysql_fatal_error("Could not delete: ".$conn->error)); // Error with prepare statement
		$stmt->close(); // Close statement
		return true; // Return if successful delete
	}
	return false; // If prepared statement failed, return false
}

function TransactionsSearch($conn, $orderID) {
	if(is_null($conn)) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Transactions WHERE orderID=?;")) { // Placeholders for further sanitization
		$stmt->bind_param("s", $orderID); // bind parameters for markers
		$stmt->execute(); // execute query
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row;
	}
	return null;
}

function TransactionsExists($conn, $orderID) {
	if(is_null($conn) || is_null($orderID)) return -1; // Params cannot be null
	if($stmt = $conn->prepare("SELECT * FROM Transactions WHERE orderID=?;")) {
		$stmt->bind_param("s", $orderID); // bind parameters for markers
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
	return -1; // Error with prepared statement
}