<?php
/****************************
 * Notes
 * Validate inputs using JS on the form
 *
 */

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
// Test connection: $conn->connect_error will not be printed (for debugging purposes only)
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error));

/** Print form for item creation */
PrintCreateItem(); // Print the webpage

/** If submit button clicked and all the fields are set: Create Item */
if(isset($_POST['itemID']) && isset($_POST['title']) && isset($_POST['price']) && isset($_POST['weight']) && isset($_POST['description'])) {
	$itemID = get_post($conn, 'itemID'); // Retrieve itemID
	// Checks if item exists and inserts if item DNE, insert. Returns true if successful insert, else false for failure.
	if(ItemInsert($conn, $itemID, get_post($conn, 'title'), get_post($conn, 'price'), get_post($conn, 'weight'), get_post($conn, 'description')))
		echo "Item created!";
	else
		echo "Error: Item already exists!"; // Replace with JS message
}

$conn->close(); // Close the connection before exiting











/**
 * Prints webpage to create user
 */
function PrintCreateItem() {
	echo <<<_END
	<h1>Create Item</h1>
	<form action="CreateItem.php" method="post" enctype='multipart/form-data'>
	<pre>
	Item ID <input type="text" name="itemID" minlength="4" maxlength="4" size="4">
	Title <input type="text" name="title" maxlength="50">
	Price <input type="number" name="price" step="0.01" min="0.01" max="999999.99">
	Weight <input type="number" name="weight" step="0.01" min="0.01" max="999999.99">
	Description <input type="text" name="description" maxlength="32765">
	<input type="submit" value="Create Item">
	</pre>
	</form>
	_END;
}


function CreateItem($conn) {
	$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Get itemID & sanitize
	$title = sanitizeMySQL($conn, get_post($conn, 'title')); // Get title & sanitize
	$price = sanitizeMySQL($conn, get_post($conn, 'price')); // Get price & sanitize
	$weight = sanitizeMySQL($conn, get_post($conn, 'weight')); // Get weight & sanitize
	$description = sanitizeMySQL($conn, get_post($conn, 'description')); // Get description & sanitize
	$picture = uploadPicture($itemID); // Get directory of where the picture was stored
	/* Insert item into DB */
	echo $itemID.$title.$price.$weight.$description.$picture;
	ItemInsert($conn, $itemID, $title, $price, $weight, $description, $picture);
}

/**
 * Valid all inputs and make sure they are correct type and format
 * @param $itemID
 * @param $title
 * @param $price
 * @param $weight
 * @param $description
 */
function ValidateInputs($itemID, $title, $price, $weight, $description) {

}

/*function uploadPicture($itemID) {
	// https://www.w3schools.com/php/php_file_upload.asp
	$folder = "Pictures/";
	$name = $itemID.$_FILES['picture']['name']; // Get filename
	if($_FILES['picture']['type'] == 'image/jpeg'
		|| $_FILES['filename']['type'] == 'image/jpg'
		|| $_FILES['filename']['type'] == 'image/png') { // Validate file
		$name = strtolower(preg_replace("[^A-Za-z0-9.]", "", $name)); // Sanitizie the name
		$name = $name;
		move_uploaded_file($_FILES['filename']['tmp_name'], $name); // Move uploaded file
		//echo "Uploaded text file '$name' as '$name':<br>"; // Inform user of name change
	} else
		echo "'$name' is not an accepted text file!<br>".$_FILES['filename']['type']; // If file is not image
	return $name;
}*/