<?php
/** Import methods */
require_once 'Login.php'; // Import DB credentials
require_once 'ItemMethods.php';
require_once 'InventoryMethods.php';
// require_once 'DatabaseSecurityMethods.php'; // Can access from same package
// require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Directory of file
// require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Directory of file

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
	echo "--> Table 'Item' Inventory successfully!<br>";

	/** Picture(itemID, picture) */
	//PicturesInitialize();
	/** Category(itemID, category) */
	//CategoryInitialize();

	/** User(userID, first name, last name, email, phone, address) */

	/** Account(userID, username, password, accountType) */


}