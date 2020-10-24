<?php
require_once 'Login.php'; // Import database credentials
require_once 'ItemMethods.php'; // Load item database methods
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) // Check connection. If cannot connect to DB, terminate program.
	die(mysql_fatal_error("Could not access DB when building Item table: ".$conn->error));

$itemID = "12w4";
$title = "ABC";
$price = 32.12;
$weight = 23.21;
$description = "223";
$picture = "2aaas";
ItemInsert($conn, $itemID, $title, $price, $weight, $description, $picture);

$conn->close(); // Close the connection before exiting