<?php

/** Import methods */
require_once '../Data Layer/ItemMethods.php'; // Load item database methods
require_once '../Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once '../Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization
require_once '../Data Layer/CategoryMethods.php';
require_once '../Data Layer/PictureMethods.php';

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Retrieve itemID */
$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page

/** If itemID is null, go to ManageItems.php */
if(!$itemID) header("Location: ManageItems.php"); // If no itemID passed in, go to item management


echo <<<_END
<html>
   <head>
      <title>Update Item</title>
<link rel="stylesheet" href="../CSS/bootstrap.min.css">
<script src="../JS/jquery-3.2.1.slim.min.js"></script>
<script src="../JS/popper.min.js"></script>
<script src="../JS/bootstrap.min.js"></script>
<script src="../JS/jquery-1.10.2.js"></script>
   </head>
   <body>
      <div id="nav-placeholder"></div>
      <script>
         $.get("./admin_navbar.html", function(data) {
             $("#nav-placeholder").replaceWith(data);
         });
      </script>
      <div class="container">
         <h1>Update Item</h1>
         <hr>
        <a href="ManageItems.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Manage Items</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Admin Portal</button>
        </a>
_END;

/** Update item */
if(isset($_POST['UpdateItem'])) {
	if(isset($_POST['itemID'], $_POST['title'], $_POST['price'], $_POST['weight'],
			$_POST['description'], $_POST['quantityA'], $_POST['quantityB']) && $_POST['description'] != "") { // Check if submit button clicked and if itemID is not null
		UpdateItem($conn, $itemID, get_post($conn, 'title'), get_post($conn, 'price'), get_post($conn, 'weight'), get_post($conn, 'description'));
		echo '<div class="alert alert-primary" role="alert">Item '.$itemID.' updated!</div>';

		/** Update inventory A */
		if(isset($_POST['quantityA']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
			UpdateInventoryQuantity($conn, $itemID, 'A', get_post($conn, 'quantityA'));
			echo '<div class="alert alert-primary" role="alert">Inventory B updated!</div>';
		}

		/** Update inventory B */
		if(isset($_POST['quantityB']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
			UpdateInventoryQuantity($conn, $itemID, 'B', get_post($conn, 'quantityB'));
			echo '<div class="alert alert-primary" role="alert">Inventory A updated!</div>';
		}

	} else echo '<div class="alert alert-primary" role="alert">Fields cannot be null!</div>';
}

/** Delete item */
if(isset($_POST['DeleteItem']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	DeleteItem($conn, $itemID);
	echo '<div class="alert alert-primary" role="alert">Item '.$itemID.' deleted!</div>';
	header("Location: ManageItems.php");
}


$item = SearchItem($conn, $itemID); // Get item from DB
$inventoryA = SearchInventory($conn, $itemID, 'A');
$inventoryB = SearchInventory($conn, $itemID, 'B');

echo <<<_END
         <form action="UpdateItem.php" method="post">
            <div class="form-group">
               <label for="itemID">Product ID</label>
               <input type="text" class="form-control" aria-describedby="itemIDHelp"
                  id="itemID" name="itemID" value="{$item['itemID']}" readonly>
            </div>
            <div class="form-group">
               <label for="productTitle">Product Title</label>
               <input type="text" class="form-control" aria-describedby="productNameHelp" 
                  id="title" name="title" value="{$item['title']}" placeholder="Product title" required>
            </div>
            <div class="form-row">
               <div class="col">
                    <label for="price">Price (\$)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01"
                           max="999999.99" aria-describedby="productWeightHelp" 
                           placeholder="Enter product price" value="{$item['price']}" required>
               </div>
               <div class="col">
                  <div class="form-group">
                    <label for="weight">Weight (lb.)</label>
                    <input type="number" class="form-control" id="weight" name="weight" step="0.01"
                           min="0.01" max="999999.99" aria-describedby="productSizeHelp"
                           placeholder="Enter product Size" value="{$item['weight']}" required>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <label for="description">Product Description</label>
               <textarea class="form-control" id="description" name="description" rows="3" 
               placeholder="Product description ...">{$item['description']}</textarea>
            </div>
         
         
         
         <div class="form-group">
            <label for="quantity">Warehouse A</label>
            <input type="number" class="form-control" id="quantityA" name="quantityA" min="0" max="32000"
                   placeholder="Quantity in stock" value="{$inventoryA['quantity']}" required>
             <a>Last updated: {$inventoryA['last_update']} </a>
        </div>
        <div class="form-group">
            <label for="quantity">Warehouse B</label>
            <input type="number" class="form-control" id="quantityB" name="quantityB" min="0" max="32000"
                   placeholder="Quantity in stock" value="{$inventoryB['quantity']}" required>
                   
             <a>Last updated: {$inventoryB['last_update']} </a>
        </div>
        
        
            <input type="hidden" id="itemID" name="itemID" value="{$item['itemID']}">
            <button type="submit" class="btn btn-primary" id="UpdateItem" name="UpdateItem">Update</button>
            <button type="submit" class="btn btn-primary" id="DeleteItem" name="DeleteItem">Delete</button>
         </form>
      </div>
   </body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting