<?php
/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection


ItemInitialize($conn);
TestViewItem();
TestManageItem();

function TestViewItem() {
	echo <<<_END
	<form action="ViewItem.php" method="post">
		<label for="itemID">Item ID</label><br>
 		<input type="text" id="itemID" name="itemID" value="1234"><br>
 		<input type="submit" id="TestViewItem" name="TestViewItem" value="Submit">
	</form>
_END;
}

function TestManageItem() {
	echo <<<_END
	<form action="ManageItem.php" method="post">
		<label for="itemID">Item ID</label><br>
 		<input type="text" id="itemID" name="itemID" value="1234"><br>
 		<input type="submit" id="TestManageItem" name="TestManageItem" value="Submit">
	</form>
_END;
}