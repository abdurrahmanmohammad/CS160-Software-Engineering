<?php
/****************************
 * Notes
 * Validate inputs using JS on the form
 *
 */

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import DB credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item DB methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PictureMethods.php'; // Load picture DB methods

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
// Test connection: $conn->connect_error will not be printed (for debugging purposes only)
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error));

/** If submit button clicked and all the fields are set: Create Item */
if(isset($_POST['itemID']) && isset($_POST['title']) && isset($_POST['price']) && isset($_POST['weight']) && isset($_POST['description']) && isset($_FILES['picture'])) {
	$itemID = get_post($conn, 'itemID'); // Retrieve itemID
	// Checks if item exists and inserts if item DNE
	if(CreateItem($conn)) echo "Item created!"; // Replace with JS message
	else echo "Error: Item already exists!"; // Replace with JS message
}

/** Close the connection before exiting */
$conn->close(); // Close the connection before exiting

/** Prints webpage to create user */
echo <<<_END
<h1>Create Item</h1>
<form action="CreateItem.php" method="post" enctype='multipart/form-data'>
<pre>
Item ID <input type="text" name="itemID" minlength="4" maxlength="4" size="4">
Title <input type="text" name="title" maxlength="50">
Price <input type="number" name="price" step="0.01" min="0.01" max="999999.99">
Weight <input type="number" name="weight" step="0.01" min="0.01" max="999999.99">
Description <input type="text" name="description" maxlength="32765">
Picture <input type='file' name='picture' id='picture' size='10'>
<input type="submit" value="Create Item">
</pre>
</form>
_END;

/**
 * Create item and insert in DB
 * @param $conn
 * @return bool
 */
function CreateItem($conn) {
	/* Retrieve and sanitize inputs */
	$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Get itemID & sanitize
	$title = sanitizeMySQL($conn, get_post($conn, 'title')); // Get title & sanitize
	$price = sanitizeMySQL($conn, get_post($conn, 'price')); // Get price & sanitize
	$weight = sanitizeMySQL($conn, get_post($conn, 'weight')); // Get weight & sanitize
	$description = sanitizeMySQL($conn, get_post($conn, 'description')); // Get description & sanitize
	/* Upload picture and retrieve filename */
	$picture = uploadPicture(get_post($conn, 'title')); // Get directory of where the picture was stored
	if(is_null($picture)) return false;
	/* Insert picture into DB */
	if(!PictureInsert($conn, $itemID, $picture)) { // Try to insert picture in DB
		echo "Picture insert failed!<br>"; // Replace with JS message
		return false;
	}
	/* Insert item into DB */
	if(!ItemInsert($conn, $itemID, $title, $price, $weight, $description)) { // Try to item picture in DB
		echo "Item insert failed!<br>"; // Replace with JS message
		return false; // Try to item picture in DB
	}
	return true; // Successful item creation
}

/**
 * Upload an item picture
 * @param $title
 * @return string|null
 */
function uploadPicture($title) {
	// https://www.w3schools.com/php/php_file_upload.asp
	$target_dir = $_SERVER['DOCUMENT_ROOT']."/Pictures/"; // Target folder to store picture
	$extension = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION)); // Get file extension

	/* Check if file is a valid picture */
	if(!$check = exif_imagetype($_FILES["picture"]["tmp_name"])) {
		echo "File is not an image!<br>"; // Replace with JS message
		return null; // Fail to upload
	}

	/* Create a unique filename */
	$i = 0;
	while(file_exists($target_dir.$title.$i.".".$extension)) $i++; // Get a unique filename
	$filename = $target_dir.$title.$i.".".$extension; // Filename: ex. item0.jpg

	/* Check if file is too large */
	/*if($_FILES["picture"]["size"] > 500000) {
		echo "Picture is too large!<br>";
		return false; // Fail to upload
	}*/

	/* Only certain types of pictures are allowed: jpg, jpeg, png, gif */
	if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif") {
		echo "Invalid type!<br>Given: $extension<br>Allowed: JPG, JPEG, PNG & GIF<br>";
		return null; // Fail to upload
	}

	/* Upload file to server */
	if(move_uploaded_file($_FILES["picture"]["tmp_name"], $filename)) {
		echo "Picture uploaded: $filename<br>"; // Replace with JS message
	} else {
		echo "Could not upload: $filename<br>"; // Replace with JS message
		return null;
	}
	return $filename; // Return the filename
}