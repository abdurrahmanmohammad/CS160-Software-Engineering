<?php
require_once 'DatabaseMethods.php';
/**
 * ###############################################################################################################
 * ########## Orders(orderID, email, order_total, order_weight, shipping_option, address, date_placed)  ##########
 * ########## orderID: CHAR(8)              # Store a 8 char order ID                                   ##########
 * ########## email: VARCHAR(50)            # Store a 50 char email                                     ##########
 * ########## grand_total: DECIMAL(8,2)     # Store grand total                                         ##########
 * ########## order_total: DECIMAL(8,2)     # Store order total                                         ##########
 * ########## shipping_cost: DECIMAL(8,2)    # Store shipping cost                                      ##########
 * ########## order_weight: DECIMAL(8,2)    # Store order weight                                        ##########
 * ########## shipping_option: VARCHAR(8)   # Store a 8 char order option                               ##########
 * ########## address: VARCHAR(100)         # Store a max 100 char address                              ##########
 * ########## date_placed: TIMESTAMP        # Timestamp of order placement                              ##########
 * ########## delivered: BOOLEAN            # Stores if order was delivered                             ##########
 * ###############################################################################################################
 */

function InitializeOrders($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Orders ( 
		orderID CHAR(8) NOT NULL,
		email VARCHAR(50) NOT NULL,
		grand_total DECIMAL(8,2) NOT NULL,
		order_total DECIMAL(8,2) NOT NULL,
		shipping_cost DECIMAL(8,2) NOT NULL,
		order_weight DECIMAL(8,2) NOT NULL,
		shipping_option VARCHAR(8) NOT NULL,
		address VARCHAR(100) NOT NULL,
		date_placed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		delivered BOOLEAN NOT NULL,
		PRIMARY KEY (orderID)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

function InsertOrder($conn, $orderID, $email, $grand_total, $order_total, $shipping_cost, $order_weight, $shipping_option, $address) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	if(!$email) mysql_fatal_error("Email cannot be null");
	if(!$grand_total) mysql_fatal_error("Grand total cannot be null");
	if(!$order_total) mysql_fatal_error("Order total cannot be null");
	//if(!$shipping_cost) mysql_fatal_error("Shipping cost cannot be null");
	if(!$order_weight) mysql_fatal_error("Order weight cannot be null");
	if(!$shipping_option) mysql_fatal_error("Shipping option cannot be null");
	if(!$address) mysql_fatal_error("Address cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Orders VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL, 0);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ssddddss', $orderID, $email, $grand_total, $order_total, $shipping_cost, $order_weight, $shipping_option, $address))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

function DeleteOrder($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Orders WHERE orderID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

/**
 * Change the delivered field of a particular order
 * @param $conn
 * @param $orderID
 * @param $delivered
 * @return mixed
 */
function UpdateDelivered($conn, $orderID, $delivered) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	//if(!$delivered) mysql_fatal_error("Delivered cannot be null");
	/** Update: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("Update Orders SET delivered=? WHERE orderID=?;"))
		mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ds', $delivered, $orderID))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute())
		mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$affected_rows = $stmt->affected_rows; // Store number of rows affected
	$stmt->close(); // Close statement
	return $affected_rows; // Return number of rows affected by update
}


function SearchOrdersByOrdersID($conn, $orderID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$orderID) mysql_fatal_error("Order ID cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Orders WHERE orderID=? ORDER BY date_placed DESC;"))
		mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $orderID))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute())
		mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful delete
}

function SearchOrdersByEmail($conn, $email) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$email) mysql_fatal_error("Email cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Orders WHERE email=? ORDER BY date_placed DESC;"))
		mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $email))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute())
		mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
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

function SearchOrdersByShippingAndDelivered($conn, $shipping, $delivered) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$shipping) mysql_fatal_error("Shipping cannot be null");
	//if(!$delivered) mysql_fatal_error("Delivered cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Orders WHERE shipping_option=? AND delivered=? ORDER BY date_placed DESC;"))
		mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('sd', $shipping, $delivered))
		mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
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
 * Generate a random, unique 8 digit order ID
 * @param $conn
 * @return string|null
 */
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


/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

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