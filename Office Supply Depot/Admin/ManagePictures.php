<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PictureMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php';

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>Manage Pictures</title>
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
        <h1>Manage Pictures</h1>
        <hr>
        <a href="ManageItems.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Manage Items</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Admin Portal</button>
        </a>
_END;


$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page

/** Delete picture */
if(isset($_POST['DeletePicture']) && isset($itemID) && isset($_POST['directory'])) { // Check if submit button clicked and if itemID is not null
	DeletePicture($conn, $itemID, get_post($conn, 'directory')); // Delete the picture from the DB and server
	echo <<<_END
    <div class="alert alert-primary" role="alert">Picture deleted!</div>
    _END;
}

/** Add picture */
if(isset($itemID) && isset($_POST['AddPicture']) && isset($_FILES['picture'])) {
	$title = SearchItem($conn, $itemID)['title'];
	/* Get directory of where the picture was stored */
	$picture = UploadPicture($itemID, $title);
	/* Check if picture was uploaded */
	if($picture)
		/* Insert picture into DB */
		if(!InsertPicture($conn, $itemID, $picture)) // Try to insert picture in DB
			echo <<<_END
		<div class="alert alert-primary" role="alert">Picture insert failed!</div>
		_END; // Print error message if insert into DB failed
}


/** Print categories for a certain item */
echo <<<_END
<form action="ManagePictures.php" method="post" enctype='multipart/form-data'>
   <div class="form-group">
      <label for="picture">Upload Picture</label>
      <input type="file" class="form-control-file" name='picture' id='picture' required>
      <input type="hidden" id="itemID" name="itemID" value="$itemID">
   </div>
   <button type="submit" class="btn btn-primary" id="AddPicture" name="AddPicture">Upload</button>
</form>

<table class="table table-bordered">
<thead>
   <tr>
      <th>Picture</th>
      <th>Directory</th>
      <th>Delete</th>
   </tr>
</thead>
_END;

/* Get all pictures for an item */
$pictures = SearchPicture($conn, $itemID);

/* Print all the pictures for an item */
foreach($pictures as $picture) {
	if($picture != null)
		echo <<<_END
		<tr>
		   <td><img src="{$picture['directory']}" alt="Error" width="500"></td>
		   <td>{$picture['directory']}</td>
		   <td>
		      <form action="ManagePictures.php" method="post" enctype='multipart/form-data'>
		         <input type="hidden" id="itemID" name="itemID" value="{$picture['itemID']}">
		         <input type="hidden" id="directory" name="directory" value="{$picture['directory']}">
		         <button type="submit" class="btn btn-primary" id="DeletePicture" name="DeletePicture">Delete</button>
		      </form>
		   </td>
		</tr>
		_END;
}
echo <<<_END
</table>
</div>
</body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting

