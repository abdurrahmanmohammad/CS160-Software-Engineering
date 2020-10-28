<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/CategoryMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PictureMethods.php';

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

/** @var $itemID */
$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page
//PrintInventories($item); // Print inventory information

/** Update item */
if(isset($_POST['UpdateItem']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	ItemUpdateByItemID($conn, get_post($conn, 'OLD_itemID'), get_post($conn, 'itemID'), get_post($conn, 'title'),
		get_post($conn, 'price'), get_post($conn, 'weight'), get_post($conn, 'description'));
	echo "Item $itemID updated!"; // Replace with a JS message
}

/** Delete item */
if(isset($_POST['DeleteItem']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	ItemDelete($conn, $itemID, null, null, null, null);
	echo "Item $itemID deleted!"; // Replace with a JS message
}

/** Update inventory A */
if(isset($_POST['UpdateInventoryA']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	InventoryUpdateQuantity($conn, $itemID, 'A', get_post($conn, 'quantity'));
	echo "Inventory updated!"; // Replace with a JS message
}

/** Update inventory B */
if(isset($_POST['UpdateInventoryB']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	InventoryUpdateQuantity($conn, $itemID, 'B', get_post($conn, 'quantity'));
	echo "Inventory updated!"; // Replace with a JS message
}

/** Delete category */
if(isset($_POST['DeleteCategory']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	CategoryDelete($conn, $itemID, get_post($conn, 'category'));
	echo "Category deleted!!"; // Replace with a JS message
}

/** Delete picture */
if(isset($_POST['DeletePicture']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	PictureDelete($conn, $itemID, get_post($conn, 'picture'));
	echo "Picture deleted!!"; // Replace with a JS message
}

/** Print item */
PrintViewItem($conn, $itemID); // Print item information with updatable fields
PrintViewInventory($conn, $itemID);
PrintCategories($conn, $itemID);
PrintPictures($conn, $itemID);


/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting
/* ######################### End: Actual Webpage #########################*/


function PrintViewItem($conn, $itemID) {
	$item = ItemSearch($conn, $itemID, null, null, null, null)[0]; // Get item from DB
	if(is_null($item)) return; // If no item passed in, don't print anything
	echo <<<_END
	<h1>{$item['title']}</h1>
	<form action="ManageItem.php" method="post">
		ItemID: <input type="text" id="itemID" name="itemID" value="{$item['itemID']}">
		<br>
		Title: <input type="text" id="title" name="title" value="{$item['title']}">
		<br>
		Price: <input type="text" id="price" name="price" value="{$item['price']}">
		<br>
		Weight: <input type="text" id="weight" name="weight" value="{$item['weight']}">
		<br>
		Description: <input type="text" id="description" name="description" value="{$item['description']}">
		<br>
		<input type="hidden" id="OLD_itemID" name="OLD_itemID" value="{$item['itemID']}">
		<input type="submit" id="UpdateItem" name="UpdateItem" value="Update">
		<input type="submit" id="DeleteItem" name="DeleteItem" value="Delete">
	</form>
	_END;
}

function PrintViewInventory($conn, $itemID) {
	$inventory = InventorySearch($conn, $itemID, null, null, null);
	$inventoryA = $inventory[0];
	$inventoryB = $inventory[1];

	echo <<<_END
	<h1>Inventory: Warehouse A</h1>
	<form action="ManageItem.php" method="post">
		ItemID: {$inventoryA['itemID']}
		<br>
		Warehouse: {$inventoryA['warehouse']}
		<br>
		Quantity: <input type="text" id="quantity" name="quantity" value="{$inventoryA['quantity']}">
		<br>
		Last update: {$inventoryB['last_update']}
		<br>
		<input type="hidden" id="itemID" name="itemID" value="{$inventoryA['itemID']}">
		<input type="submit" id="UpdateInventoryA" name="UpdateInventoryA" value="Update">
		<br>
	</form>
	<h1>Inventory: Warehouse B</h1>
	<form action="ManageItem.php" method="post">
		ItemID: {$inventoryA['itemID']}
		<br>
		Warehouse: {$inventoryA['warehouse']}
		<br>
		Quantity: <input type="text" id="quantity" name="quantity" value="{$inventoryB['quantity']}">
		<br>
		Last update: {$inventoryB['last_update']}
		<br>
		<input type="hidden" id="itemID" name="itemID" value="{$inventoryA['itemID']}">
		<input type="submit" id="UpdateInventoryB" name="UpdateInventoryB" value="Update">
		<br>
	</form>
	_END;
}

function PrintCategories($conn, $itemID) {
	$categories = CategorySearch($conn, $itemID);
	if(is_null($categories)) return; // If no item passed in, don't print anything
	echo <<<_END
		<h1>Categories</h1>
	_END;
	foreach($categories as $category)
		echo <<<_END
		<form action="ManageItem.php" method="post">
		$category[1]
		<input type="hidden" id="category" name="category" value="$category[1]">
		<input type="hidden" id="itemID" name="itemID" value="$category[0]">
		<input type="submit" id="DeleteCategory" name="DeleteCategory" value="Delete">
		</form>
		_END;
}

function PrintPictures($conn, $itemID) {
	$pictures = PictureSearch($conn, $itemID);
	if(is_null($pictures)) return; // If no item passed in, don't print anything
	echo <<<_END
		<h1>Pictures</h1>
	_END;

	foreach($pictures as $picture)
		echo <<<_END
		<form action="ManageItem.php" method="post">
		$picture[1]
		<input type="hidden" id="picture" name="picture" value="$picture[1]">
		<input type="hidden" id="itemID" name="itemID" value="$picture[0]">
		<input type="submit" id="DeletePicture" name="DeletePicture" value="Delete">
		</form>
		_END;
}