<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate();

echo <<<_END
<html>
<head>
<title>View Items</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.2.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_admin.html", function(data){
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Items</h1>
        <hr>
        <a href="CreateItem.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Create Item</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
</body>
</html>
_END;
/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection


if(isset($_POST['delete']) && isset($_POST['itemID'])) {
	ItemDelete($conn, get_post($conn, 'itemID'), null, null, null, null);
	/* Print confirmation message */
	echo <<<_END
	<div class="alert alert-primary" role="alert">Item deleted!</div>
	_END;
}


PrintItems($conn);


function PrintItems($conn) {
	$items = ItemSearch($conn, null, null, null, null, null); // Get all items
	echo <<<_END
	<table class="table table-bordered">
	<thead>
	   <tr>
	      <th>Item ID</th>
	      <th>Title</th>
	      <th>Price ($)</th>
	      <th>Weight (oz)</th>
	      <th>Description</th>
	      <th>Categories</th>
	      <th>Pictures</th>
	      <th>Update</th>
	      <th>Delete</th>
	   </tr>
	</thead>
	_END;

	foreach($items as $item) {
		if($item != null)
		echo <<<_END
		<tr>
		   <td>{$item['itemID']}</td>
		   <td>{$item['title']}</td>
		   <td>{$item['price']}</td>
		   <td>{$item['weight']}</td>
		   <td>{$item['description']}</td>
		   <td>
		      <form action="ManageCategories.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" name="itemID" value="{$item['itemID']}">
		         <button type="submit" class="btn btn-primary" name="update">Manage</button>
		      </form>
		   </td>
		   <td>
		      <form action="ManagePictures.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" name="itemID" value="{$item['itemID']}">
		         <button type="submit" class="btn btn-primary" name="delete">Manage</button>
		      </form>
		   </td>
		   <td>
		      <form action="UpdateItem.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" name="OLD_itemID" value="{$item['itemID']}">
		         <button type="submit" class="btn btn-primary" name="update">Update</button>
		      </form>
		   </td>
		   <td>
		      <form action="ListItems.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" name="itemID" value="{$item['itemID']}">
		         <button type="submit" class="btn btn-primary" name="delete">Delete</button>
		      </form>
		   </td>
		</tr>
		_END;
	}
	echo <<<_END
	</table>
	_END;
}