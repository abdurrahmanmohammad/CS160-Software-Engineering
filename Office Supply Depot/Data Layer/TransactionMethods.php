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

function InitializeTransactions($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
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
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

function InsertTransaction($conn, $orderID, $order_total, $card_holder, $credit_card, $card_month, $card_year, $cvc) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	if(!$order_total) mysql_fatal_error("Order Total cannot be null");
	if(!$card_holder) mysql_fatal_error("Card Holder cannot be null");
	if(!$credit_card) mysql_fatal_error("Credit Card cannot be null");
	if(!$card_month) mysql_fatal_error("Card Month cannot be null");
	if(!$card_year) mysql_fatal_error("Card Year cannot be null");
	if(!$cvc) mysql_fatal_error("CVC cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Transactions VALUES (?, ?, ?, ?, ?, ?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('sdsssss', $orderID, $order_total, $card_holder, $credit_card, $card_month, $card_year, $cvc))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

function DeleteTransaction($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Transactions WHERE orderID=?;"))
		mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function SearchTransaction($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Transactions WHERE orderID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get result */
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
 * Checks if a certain transaction exists
 * Update: Use the search method instead
 * @param $conn
 * @param $orderID
 * @return mixed
 */
function TransactionsExists($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	/** Perform query */
	if(!$stmt = $conn->prepare("SELECT * FROM Transactions WHERE orderID=?;"))
		mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Store result */
	if(!$stmt->store_result()) mysql_fatal_error("Store result failed : ".$conn->error);
	$rowcount = $stmt->num_rows; // Get rowcount
	$stmt->close(); // Close statement
	return $rowcount; // Return rowcount
}