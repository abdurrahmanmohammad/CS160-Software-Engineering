<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

/** @var $itemID */
$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page

/** @var $item */
$item = ItemSearch($conn, $itemID, null, null, null, null)[0]; // Get item from DB

/** Add item to cart */
if(isset($_POST['AddToCart']) && isset($itemID)) // Check if submit button clicked and if itemID is not null
	echo "Item $itemID added to cart!";
	//AddToCart($conn, $itemID); // Add item to cart

/** Print item */
PrintViewItem($item); // Print the item


/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting
/* ######################### End: Actual Webpage #########################*/

/**
 * @param $item
 */
function PrintViewItem($item) {
	if(is_null($item)) return; // If no item passed in, don't print anything
	echo <<<_END
	<h1>ItemID: {$item['itemID']}</h1>
	<h1>Title {$item['title']}</h1>
	<h1>Price {$item['price']}</h1>
	<h1>Weight {$item['weight']}</h1>
	<h1>Description {$item['description']}</h1>
	<form action="ViewItem.php" method="post">
		<input type="submit" id="AddToCart" name="AddToCart" value="Add to Cart">
	</form>
	_END;
}