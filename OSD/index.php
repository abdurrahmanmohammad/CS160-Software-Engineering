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
	<title>Home</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery-3.2.1.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-1.10.2.js"></script>
</head>
<body>
<div id="nav-placeholder"></div>
<script>
    $.get("./nav.html", function (data) {
        $("#nav-placeholder").replaceWith(data);
    });
</script>
<h1>Office Supply Depot</h1>
<h3>Products</h3>
<table>
_END;

PrintProducts($conn);

echo <<<_END
</table>
</body>
</html>
_END;

function PrintProducts($conn) {
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

}
