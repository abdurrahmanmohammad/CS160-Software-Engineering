<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/CategoryMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PictureMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "customer") === 0) header("Location: CustomerPortal.php"); // Customer cannot use admin portal
else if(strcmp($account['accountType'], "admin") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
   <head>
      <title>Update Item</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
         integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
         integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
         integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
         integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
   </head>
   <body>
      <div id="nav-placeholder"></div>
      <script>
         $.get("./nav_admin.html", function(data) {
             $("#nav-placeholder").replaceWith(data);
         });
      </script>
      <div class="container">
         <h1>Update Item</h1>
         <hr>
        <a href="./ListItems.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Manage Items</button>
        </a>
         <a href="../../AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Admin Portal</button>
        </a>
_END;

/** @var $itemID */
$itemID = sanitizeMySQL($conn, get_post($conn, 'OLD_itemID')); // Extract item ID from previous page
//PrintInventories($item); // Print inventory information


/** Update item */
if(isset($_POST['UpdateItem']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	ItemUpdateByItemID($conn, get_post($conn, 'OLD_itemID'), get_post($conn, 'itemID'), get_post($conn, 'title'),
		get_post($conn, 'price'), get_post($conn, 'weight'), get_post($conn, 'description'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Item $itemID updated!</div>
    _END;
}

/** Delete item */
if(isset($_POST['DeleteItem']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	ItemDelete($conn, $itemID, null, null, null, null);
	echo <<<_END
    <div class="alert alert-primary" role="alert">Item $itemID deleted!</div>
    _END;
	header("Location: ListItems.php");
}

/** Update inventory A */
if(isset($_POST['quantityA']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	InventoryUpdateQuantity($conn, $itemID, 'A', get_post($conn, 'quantityA'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Inventory B updated!</div>
    _END;
}

/** Update inventory B */
if(isset($_POST['quantityB']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	InventoryUpdateQuantity($conn, $itemID, 'B', get_post($conn, 'quantityB'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Inventory A updated!</div>
    _END;
}



$item = ItemSearch($conn, $itemID, null, null, null, null)[0]; // Get item from DB
$inventory = InventorySearch($conn, $itemID, null, null, null);
$inventoryA = $inventory[0];
$inventoryB = $inventory[1];

echo <<<_END
         <form action="UpdateItem.php" method="post">
            <div class="form-group">
               <label for="itemID">Product ID</label>
               <input type="text" class="form-control" aria-describedby="itemIDHelp"
                  id="itemID" name="itemID" value="{$item['itemID']}" placeholder="Enter itemID" required>
            </div>
            <div class="form-group">
               <label for="productTitle">Product Title</label>
               <input type="text" class="form-control" aria-describedby="productNameHelp" 
                  id="title" name="title" value="{$item['title']}" placeholder="Product title" required>
            </div>
            <div class="form-row">
               <div class="col">
                  <label for="price">Product Price</label>
                  <input type="number" class="form-control" aria-describedby="productSizeHelp"
                     id="price" name="price" step="0.01" min="0.01" max="999999.99"
                     value="{$item['price']}" placeholder="Enter product price" required>
               </div>
               <div class="col">
                  <div class="form-group">
                     <label for="weight">Product Weight</label>
                     <input type="text" class="form-control" aria-describedby="productWeightHelp" 
                        id="weight" name="weight" tep="0.01" min="0.01" max="999999.99"
                        value="{$item['weight']}" placeholder="Enter product Weight" required>
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
            <input type="number" class="form-control" min="0" max="32000"
                   id="quantityA" name="quantityA" value="{$inventoryA['quantity']}" placeholder="Quantity in stock" required>
             <a>Last updated: {$inventoryA['last_update']} </a>
        </div>
        <div class="form-group">
            <label for="quantity">Warehouse B</label>
            <input type="number" class="form-control" min="0" max="32000"
                   id="quantityB" name="quantityB" value="{$inventoryB['quantity']}" placeholder="Quantity in stock" required>
             <a>Last updated: {$inventoryB['last_update']} </a>
        </div>
        
        
            <input type="hidden" id="OLD_itemID" name="OLD_itemID" value="{$item['itemID']}">
            <button type="submit" class="btn btn-primary" id="UpdateItem" name="UpdateItem">Update</button>
            <button type="submit" class="btn btn-primary" id="DeleteItem" name="DeleteItem">Delete</button>
         </form>
      </div>
   </body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting