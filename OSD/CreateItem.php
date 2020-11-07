<?php
/****************************
 * Notes
 * Validate inputs using JS on the form
 *
 */

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import DB credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item DB methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory DB methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PictureMethods.php'; // Load picture DB methods

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
// Test connection: $conn->connect_error will not be printed (for debugging purposes only)
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error));

/** If submit button clicked and all the fields are set: Create Item */
if(isset($_POST['itemID']) && isset($_POST['title']) && isset($_POST['price']) && isset($_POST['weight']) && isset($_POST['description'])
	&& isset($_FILES['picture']) && isset($_POST['quantityA']) && isset($_POST['quantityB'])) {
	$itemID = get_post($conn, 'itemID'); // Retrieve itemID
	// Checks if item exists and inserts if item DNE
	if(CreateItem($conn)) echo "Item created!"; // Replace with JS message
	else echo "Error: Item already exists!"; // Replace with JS message
}

/** Close the connection before exiting */
$conn->close(); // Close the connection before exiting

/** Prints webpage to create user */
echo <<<_END
<html>
<head>
    <title>Create Item</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>

<body>
<div id="nav-placeholder"></div>
<script>
    $.get("./nav.html", function (data) {
        $("#nav-placeholder").replaceWith(data);
    });
</script>
<div class="container">
    <h1>Create Item</h1>
    <hr>
    <form action="CreateItem.php" method="post" enctype='multipart/form-data'>
        <div class="form-group">
            <label for="itemID">Product ID</label>
            <input type="text" class="form-control" id="itemID" name="itemID" minlength="4" maxlength="4"
                   aria-describedby="productNameHelp"
                   placeholder="Enter product name" required>
        </div>
        <div class="form-group">
            <label for="title">Product Title</label>
            <input type="text" class="form-control" id="title" name="title" maxlength="50"
                   aria-describedby="productNameHelp"
                   placeholder="Enter product name" required>
        </div>
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="price">Weight</label>
                    <input type="text" class="form-control" id="price" name="price" step="0.01" min="0.01"
                           max="999999.99"
                           aria-describedby="productWeightHelp" placeholder="Enter product Weight" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="weight">Price</label>
                    <input type="text" class="form-control" id="weight" name="weight" step="0.01"
                           min="0.01" max="999999.99" aria-describedby="productSizeHelp"
                           placeholder="Enter product Size" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Product Description</label>
            <textarea class="form-control" id="description" name="description" maxlength="32765" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="picture">Upload Product Image</label>
            <input type="file" class="form-control-file" name='picture' id='picture' required>
        </div>
        <div class="form-group">
            <label for="quantity">Warehouse A</label>
            <input type="number" class="form-control" id="quantity" name="quantityA" min="-32000" max="32000"
                   placeholder="Quantity in stock" required>
        </div>
        <div class="form-group">
            <label for="quantity">Warehouse B</label>
            <input type="number" class="form-control" id="quantity" name="quantityB" min="-32000" max="32000"
                   placeholder="Quantity in stock" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>

</body>

</html>
_END;

/*echo <<<_END
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
_END;*/

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
	$quantityA = sanitizeMySQL($conn, get_post($conn, 'quantityA')); // Get quantity & sanitize
	$quantityB = sanitizeMySQL($conn, get_post($conn, 'quantityA')); // Get quantity & sanitize
	$description = sanitizeMySQL($conn, get_post($conn, 'description')); // Get description & sanitize
	//echo $itemID."<br>".$title."<br>".$price."<br>".$weight."<br>".$quantityA."<br>".$quantityB."<br>".$description;
	/* Upload picture and retrieve filename */
	$picture = uploadPicture(get_post($conn, 'title')); // Get directory of where the picture was stored
	if(is_null($picture)) return false;
	/* Insert item into DB */
	if(!ItemInsert($conn, $itemID, $title, $price, $weight, $description)) { // Try to item picture in DB
		echo "Item insert failed!<br>"; // Replace with JS message
		return false; // Try to item picture in DB
	}
	/* Insert item inventory into DB */
	if(!InventoryInsert($conn, $itemID, 'A', $quantityA, null)) { // Try to item picture in DB
		echo "Inventory A insert failed!<br>"; // Replace with JS message
		return false; // Try to item picture in DB
	}
	if(!InventoryInsert($conn, $itemID, 'B', $quantityB, null)) { // Try to item picture in DB
		echo "Inventory B insert failed!<br>"; // Replace with JS message
		return false; // Try to item picture in DB
	}
	/* Insert picture into DB */
	if(!PictureInsert($conn, $itemID, $picture)) { // Try to insert picture in DB
		echo "Picture insert failed!<br>"; // Replace with JS message
		return false;
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