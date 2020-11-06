<?php
/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
// Test connection: $conn->connect_error will not be printed (for debugging purposes only)
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error));

echo <<<_END
<html>
<head>
	<title>Office Supply Depot Product Management System</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	
	<script src="js/jquery-3.2.1.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-1.10.2.js"></script>
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div id="nav-placeholder"></div>
<script>
    $.get("./nav.html", function (data) {
        $("#nav-placeholder").replaceWith(data);
    });
</script>
<div class="jumbotron text-center">
  <h1>OSD</h1>
  <p>Product Management System</p> 
</div>
  
<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <h3>Business Information</h3>
      <p>Based in San Jose, CA. </p>
      <p>Delivers office products of all kind at a competitive</p>
      <p>price and in a timely manner.</p>
    </div>
    <div class="col-sm-4">
      <h3>Hours of Operation:</h3>
      <p>Mon - Fri 8:00 AM to 5:00 PM PCT</p>
    </div>
    <div class="col-sm-4">
      <h3>Business Location</h3>        
      <p>1 Washington Sq, San Jose, CA 95192</p>
    </div>
  </div>
</div>
</body>
</html>
_END;

//PrintProducts($conn);


function PrintProducts($conn) {
	echo <<<_END
	<table>
_END;
	$items = ItemSearch($conn, null, null, null, null, null, null);
	foreach($items as $item) {
		echo <<<_END

	<tr>
		<td>{$item['title']}</td>
   		<td>{$item['price']}</td>
   		<td>{$item['description']}</td>
	</tr>
_END;

	}
	echo <<<_END
	</table>
_END;
}
