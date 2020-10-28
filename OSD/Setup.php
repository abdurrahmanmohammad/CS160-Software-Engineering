<?php
/* ######################### Start: Setup ######################### */
// Caution: Run this file only once
require_once 'DatabaseSecurityMethods.php'; // Load database sanitization and error methods
setup(); // Sets up DB and tables
/* ######################### End: Setup ######################### */

/**
 * Create DB and add tables
 */
function setup() {
	/* Build DB */
	echo "Building database ..."."<br>";
	require_once 'Login.php'; // Import DB credentials
	buildDB(new mysqli($hn, $un, $pw), $db); // Connect to MySQL and build the database if doesn't exist
	echo "Database '$db' created successfully!"."<br>";
	$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
	echo "Connection to '$db' established!"."<br>"."<br>";

	/* Build tables */
	echo "Building tables ..."."<br>";
	buildTables($conn); // Build tables
	echo "Done!"."<br>";
}

/**
 * Builds database if it doesn't already exist
 */
function buildDB($conn, $name) {
	// Check connection. If cannot connect to MySQL, terminate program. Error not printed (for debugging)
	if($conn->connect_error) die(mysql_fatal_error("Could not access MySQL when building table: ".$conn->connect_error));
	$sql = "CREATE DATABASE IF NOT EXISTS $name"; // Build database if it does not already exist
	if($conn->query($sql) === FALSE) echo mysql_fatal_error("Error creating database: ".$conn->error); // Check if database was created successfully
	$conn->close(); // Close the connection
}

function buildTables($conn) {
	require_once 'ItemMethods.php'; // Import Item methods
	/** Item(itemID, title, price, weight, description) */
	ItemInitialize($conn, null); // Build table: Item
	echo "Table 'Item' created successfully!"."<br>";
	/** Inventory(itemID, warehouse_number, number_in_stock) */
	InventoryInitialize();
	/** Picture(itemID, picture) */
	PicturesInitialize();
	/** Category(itemID, category) */
	CategoryInitialize();

	/** User(userID, first name, last name, email, phone, address) */

	/** Account(userID, username, password, accountType) */


}