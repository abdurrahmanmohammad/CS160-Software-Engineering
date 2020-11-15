<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/CategoryMethods.php';

/** Authenticate user on page */
$account = authenticate();

echo <<<_END
<html>
<head>
<title>Manage Categories</title>
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
        <h1>Manage Categories</h1>
        <hr>
        <a href="ListItems.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Manage Items</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Admin Portal</button>
        </a>
_END;

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

/** @var $itemID */
$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page

/** Delete category */
if(isset($_POST['DeleteCategory']) && isset($itemID) && isset($_POST['category'])) { // Check if submit button clicked and if itemID is not null
	CategoryDelete($conn, $itemID, get_post($conn, 'category'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Category deleted!</div>
    _END;
}

/** Add category */
if(isset($_POST['AddCategory']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	CategoryInsert($conn, $itemID, get_post($conn, 'category'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Category inserted!</div>
    _END;
}



/** Print categories for a certain item */
$categories = CategorySearch($conn, $itemID); // Get all categories for an item
echo <<<_END
<form action="ManageCategories.php" method="post">
   Add Category <input type="text" id="category" name="category">
   <input type="hidden" id="itemID" name="itemID" value="$itemID">
   <button type="submit" class="btn btn-primary" id="AddCategory" name="AddCategory">Add</button>
</form>

<table class="table table-bordered">
<thead>
   <tr>
      <th>Category</th>
      <th>Delete</th>
   </tr>
</thead>
_END;

/** Print all the categories for an item */
foreach($categories as $category) {
	if($category != null)
		echo <<<_END
		<tr>
		   <td>{$category['category']}</td>
		   <td>
		      <form action="ManageCategories.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" id="itemID" name="itemID" value="{$category['itemID']}">
		         <input type="hidden" id="category" name="category" value="{$category['category']}">
		         <button type="submit" class="btn btn-primary" id="DeleteCategory" name="DeleteCategory">Delete</button>
		      </form>
		   </td>
		</tr>
		_END;
}
echo <<<_END
</table>
</body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting

