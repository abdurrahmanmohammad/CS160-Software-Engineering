<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Load picture DB methods

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>View Items</title>
<link rel="stylesheet" href="../CSS/bootstrap.min.css">
<script src="../JS/jquery-3.2.1.slim.min.js"></script>
<script src="../JS/popper.min.js"></script>
<script src="../JS/bootstrap.min.js"></script>
<script src="../JS/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./admin_navbar.html", function(data){
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

if(isset($_POST['delete']) && isset($_POST['itemID'])) {
	DeleteItem($conn, get_post($conn, 'itemID'));
	echo '<div class="alert alert-primary" role="alert">Item deleted!</div>'; // Print confirmation message
}


PrintItems($conn);
/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting

function PrintItems($conn) {
	$items = GetAllItems($conn); // Get all items
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
	      <th>View</th>
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
		         <input type="hidden" name="itemID" value="{$item['itemID']}">
		         <button type="submit" class="btn btn-primary" name="update">Update</button>
		      </form>
		   </td>
		   <td>
		      <form action="ManageItems.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" name="itemID" value="{$item['itemID']}">
		         <button type="submit" class="btn btn-primary" name="delete">Delete</button>
		      </form>
		   </td>
		   <td>
		   		<a href="ViewItem.php?itemID={$item['itemID']}">
		   			<button type="submit" class="btn btn-primary">View</button>
				</a>
		   </td>
		</tr>
		</tr>
		_END;
	}
	echo <<<_END
	</table>
	_END;
}