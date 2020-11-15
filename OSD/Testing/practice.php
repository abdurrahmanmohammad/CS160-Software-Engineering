<?php

/** Import methods */
require_once 'Login.php'; // Import database credentials
require_once 'ItemMethods.php'; // Load item database methods
require_once 'DatabaseMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

/** @var $itemID */
$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page

/** @var $item */
$item = ItemSearch($conn, $itemID, null, null, null, null); // Get item from DB

/** Print item */
PrintViewItem($item); // Print item information with updatable fields
//PrintInventories($item); // Print inventory information



/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting
/* ######################### End: Actual Webpage #########################*/


function PrintViewItem($item) {
	if(is_null($item)) return;
	echo <<<_END
	<form action="ManageItem.php" method="post">
		ItemID: <input type="text" id="itemID" name="itemID" value="{$item[0]['itemID']}">
		<br>
		Title: <input type="text" id="title" name="title" value="{$item[0]['title']}">
		<br>
		Price: <input type="text" id="price" name="price" value="{$item[0]['price']}">
		<br>
		Weight: <input type="text" id="weight" name="weight" value="{$item[0]['weight']}">
		<br>
		Description: <input type="text" id="description" name="description" value="{$item[0]['description']}">
		<br>
		<input type="submit" id="UpdateItem" name="UpdateItem" value="Update">
		<input type="submit" id="DeleteItem" name="DeleteItem" value="Delete">
	</form>
	_END;
}
