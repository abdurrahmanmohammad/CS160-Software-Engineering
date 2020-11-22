<?php
/****************************
 * Notes
 * Validate inputs using JS on the form
 *
 */

/** Import methods */
require_once '../Data Layer/ItemMethods.php'; // Load item DB methods
require_once '../Data Layer/InventoryMethods.php'; // Load inventory DB methods
require_once '../Data Layer/PictureMethods.php'; // Load picture DB methods
require_once '../Data Layer/DatabaseMethods.php'; // Load picture DB methods

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Prints webpage to create user */
echo <<<_END
<html>
<head>
    <title>Create Item</title>
      <link rel="stylesheet" href="../CSS/bootstrap.min.css">
      <script src="../JS/jquery-3.2.1.slim.min.js"></script>
      <script src="../JS/popper.min.js"></script>
      <script src="../JS/bootstrap.min.js"></script>
      <script src="../JS/jquery-1.10.2.js"></script>
</head>

<body>
<div id="nav-placeholder"></div>
<script>
    $.get("./admin_navbar.html", function (data) {
        $("#nav-placeholder").replaceWith(data);
    });
</script>
<div class="container">
    <h1>Create Item</h1>
    <hr>
	<a href="ManageItems.php" class="btn">
	<button type="button" class="btn btn-sm btn-outline-secondary">Manage Items</button>
	</a>
	<a href="AdminPortal.php" class="btn">
	<button type="button" class="btn btn-sm btn-outline-secondary">Admin Portal</button>
	</a>
_END;


/** If submit button clicked and all the fields are set: Create Item */
if(isset($_POST['itemID']) && isset($_POST['title']) && isset($_POST['price']) && isset($_POST['weight']) && isset($_POST['description'])
	&& isset($_FILES['picture']) && isset($_POST['quantityA']) && isset($_POST['quantityB'])) {
	$itemID = get_post($conn, 'itemID'); // Retrieve itemID
	// Checks if item exists and inserts if item DNE
	CreateItem($conn);
}

/** Close the connection before exiting */
$conn->close(); // Close the connection before exiting


echo <<<_END
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
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01"
                           max="999999.99"
                           aria-describedby="productWeightHelp" placeholder="Enter product price" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="weight">Price</label>
                    <input type="number" class="form-control" id="weight" name="weight" step="0.01"
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
            <input type="number" class="form-control" id="quantity" name="quantityA" min="0" max="32000"
                   placeholder="Quantity in stock" required>
        </div>
        <div class="form-group">
            <label for="quantity">Warehouse B</label>
            <input type="number" class="form-control" id="quantity" name="quantityB" min="0" max="32000"
                   placeholder="Quantity in stock" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>

</body>

</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting

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

	/* Insert item into DB */
	if(!ItemExists($conn, $itemID)) { // If item does not exist
		InsertItem($conn, $itemID, $title, $price, $weight, $description); // Insert item
		echo '<div class="alert alert-primary" role="alert">Item insert created!</div>';
	} else {
		echo '<div class="alert alert-primary" role="alert">Item insert failed!</div>';
		return false;
	}

	/* Insert item inventory into DB */
	if(!InsertInventory($conn, $itemID, 'A', $quantityA, null)) { // Try to item picture in DB
		echo <<<_END
    <div class="alert alert-primary" role="alert">Inventory A insert failed!</div>
    _END;
		return false; // Try to item picture in DB
	}
	if(!InsertInventory($conn, $itemID, 'B', $quantityB, null)) { // Try to item picture in DB
		echo <<<_END
    <div class="alert alert-primary" role="alert">Inventory B insert failed!</div>
    _END;
		return false; // Try to item picture in DB
	}
	/* Upload picture and retrieve filename */
	$picture = UploadPicture($itemID, $title); // Get directory of where the picture was stored
	if(is_null($picture)) return false;
	/* Insert picture into DB */
	if(!InsertPicture($conn, $itemID, $picture)) { // Try to insert picture in DB
		echo <<<_END
    <div class="alert alert-primary" role="alert">Picture insert failed!</div>
    _END;
		return false;
	} else
		echo <<<_END
		<div class="alert alert-primary" role="alert">Picture inserted!</div>
		_END;
	return true; // Successful item creation
}

