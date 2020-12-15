<?php
/**
 * #######################################################
 * ############ Creates database and tables ##############
 * ############ Caution: Run this file only once #########
 * #######################################################
 */

/** Import methods */
require_once 'Login.php'; // Import DB credentials
require_once 'AccountMethods.php';
require_once 'CartMethods.php';
require_once 'CategoryMethods.php';
require_once 'InventoryMethods.php';
require_once 'ItemMethods.php';
require_once 'OrderMethods.php';
require_once 'PictureMethods.php';
require_once 'PurchaseMethods.php';
require_once 'TransactionMethods.php';

/** Build database: OSD_Database (name included in Login.php) */
echo "#################### Building database #################### <br>";
buildDB(new mysqli($hn, $un, $pw), $db); // Connect to MySQL and build the database if it doesn't exist
echo "--> Database '$db' built successfully!<br><br>"; // Print success message

/** Establish connection to database */
echo "#################### Establishing connection ####################<br>";
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
echo "--> Connection to '$db' established!<br><br>";

/** Build tables */
echo "########## Building tables ##########<br>";
buildTables($conn); // Build tables

//echo "--> Tables successfully built!<br><br>";

echo "<br>########## Database initialized ##########<br>";

/**
 * Builds database if it doesn't already exist
 * @param $conn
 * @param $name
 */
function buildDB($conn, $name) {
	// Check connection. If cannot connect to MySQL, terminate program. Error not printed (for debugging)
	if($conn->connect_error) die(mysql_fatal_error("Could not access MySQL when building table: ".$conn->connect_error));
	$sql = "CREATE DATABASE IF NOT EXISTS $name"; // Build database if it does not already exist
	if($conn->query($sql) === FALSE) echo mysql_fatal_error("Error creating database: ".$conn->error); // Check if database was created successfully
	$conn->close(); // Close the connection
}

/**
 * Add tables to database
 * @param $conn
 */
function buildTables($conn) {
	/** Items(itemID, title, price, weight, description) */
	InitializeItems($conn);
	echo "--> Table 'Item' built successfully!<br>";

	/** Inventory(itemID, warehouse, quantity, update) */
	InitializeInventory($conn);
	echo "--> Table 'Inventory' built successfully!<br>";

	/** Pictures(itemID, directory) */
	InitializePictures($conn);
	echo "--> Table 'Pictures' built successfully!<br>";

	/** Categories(itemID, category) */
	InitializeCategories($conn);
	echo "--> Table 'Categories' built successfully!<br>";

	/** Accounts(email, password, AccountsType, first name, last name, phone, address) */
	InitializeAccounts($conn);
	echo "--> Table 'Accounts' built successfully!<br>";

	/** Delete old admin */
	DeleteAccount($conn, "admin@admin.com"); // Delete existing admin account
	echo "--> Admin deleted!<br>";

	/** Insert new admin */
	$password = password_hash("CS160", PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
	InsertAccount($conn, "admin@admin.com", $password, "admin", "admin", "admin", "admin", "admin");
	echo "--> Admin inserted!<br>";

	/** Carts(userID, itemID, multiplicity) */
	InitializeCarts($conn);
	echo "--> Table 'Carts' built successfully!<br>";

	/** Orders(orderID, email, order_total, order_weight, shipping_option, address, date_placed) */
	InitializeOrders($conn);
	echo "--> Table 'Orders' built successfully!<br>";

	/** Purchases(orderID, itemID, item_price, multiplicity) */
	InitializePurchases($conn);
	echo "--> Table 'Purchases' built successfully!<br>";

	/** Transactions(orderID, order_total, card_holder, credit_card, card_month, card_year, cvc) */
	InitializeTransactions($conn);
	echo "--> Table 'Transactions' built successfully!<br>";
}