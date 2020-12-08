<?php
/** Import DB methods */
require_once '../Data Layer/CategoryMethods.php';
require_once '../Data Layer/DatabaseMethods.php'; // Load picture DB methods

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
<title>Manage Categories</title>
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
        <h1>Manage Categories</h1>
        <hr>
        <a href="ManageItems.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Manage Items</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Admin Portal</button>
        </a>
_END;

/** Delete category */
if(isset($_POST['DeleteCategory']) && isset($itemID) && isset($_POST['category'])) { // Check if submit button clicked and if itemID is not null
	DeleteCategory($conn, $itemID, get_post($conn, 'category'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Category deleted!</div>
    _END;
}

/** Add category */
if(isset($_POST['AddCategory']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	if(!CategoryExists($conn, $itemID, get_post($conn, 'category'))) {
		InsertCategory($conn, $itemID, get_post($conn, 'category'));
		echo <<<_END
		<div class="alert alert-primary" role="alert">Category inserted!</div>
		_END;
	} else {
		echo <<<_END
		<div class="alert alert-primary" role="alert">Category exists!</div>
		_END;
	}

}


/** Print categories for a certain item */
$categories = SearchCategories($conn, $itemID); // Get all categories for an item
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

