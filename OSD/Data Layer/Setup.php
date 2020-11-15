<?php
/** Import methods */
require_once 'Login.php'; // Import DB credentials
require_once 'ItemMethods.php';
require_once 'InventoryMethods.php';
require_once 'PictureMethods.php';
require_once 'CategoryMethods.php';
require_once 'CartMethods.php';
// require_once 'DatabaseMethods.php'; // Can access from same package
// require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Directory of file
// require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Directory of file
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization


/** ####################################################### */
/** ############ Creates database and tables ############## */
/** ############ Caution: Run this file only once ######### */
/** ####################################################### */

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
	/** Item(itemID, title, price, weight, description) */
	ItemInitialize($conn);
	echo "--> Table 'Item' built successfully!<br>";

	/** Inventory(itemID, warehouse_number, number_in_stock) */
	InventoryInitialize($conn);
	echo "--> Table 'Inventory' built successfully!<br>";

	/** Picture(itemID, picture) */
	PictureInitialize($conn);
	echo "--> Table 'Picture' built successfully!<br>";

	/** Category(itemID, category) */
	CategoryInitialize($conn);
	echo "--> Table 'Category' built successfully!<br>";

	/** Account(email, password, accountType, first name, last name, phone, address) */
	AccountInitialize($conn);
	echo "--> Table 'Account' built successfully!<br>";
	$password = password_hash("CS160", PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
	AccountInsert($conn, "admin@admin.com", $password, "admin", "admin", "admin", "admin", "admin");
	echo "--> Admin inserted!<br>";

	/** Account(email, itemID, multiplicity) */
	CartInitialize($conn);
	echo "--> Table 'Cart' built successfully!<br>";
}