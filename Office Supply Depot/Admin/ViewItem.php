<?php

/* Import methods */
require_once '../Data Layer/DatabaseMethods.php';
require_once '../Data Layer/ItemMethods.php';
require_once '../Data Layer/InventoryMethods.php';
require_once '../Data Layer/PictureMethods.php';
require_once '../Data Layer/CartMethods.php';

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Get item to view */
$itemID = $_GET['itemID'];
if(!$itemID) header("Location: ManageItems.php");

$item = SearchItem($conn, $itemID); // Get item from DB
$pictures = SearchPicture($conn, $itemID); // Get item pictures
$inventoryA = SearchInventory($conn, $itemID, 'A');
$inventoryB = SearchInventory($conn, $itemID, 'B');
$inStock = $inventoryA['quantity'] + $inventoryB['quantity'];


echo <<<_END
<html>
<head>
<title>Item view</title>
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
        <h1>Item View</h1>
        <hr>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="ManageItems.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$item['title']}</li>
            </ol>
        </nav>
_END;

PrintImageCarousel($pictures);

echo <<<_END
            <div class="col">
                <h2>{$item['title']}</h2>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Price</th>
                            <th scope="col">Item ID</th>
                            <th scope="col">Weight</th>
                            <th scope="col">In stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>\${$item['price']}</td>
                            <td>$itemID</td>
                            <td>{$item['weight']}lb</td>
                            <td>$inStock</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <h3>Description</h3>

        <p>{$item['description']}</p>
    </div>
</body>

</html>
_END;


/**
 * Prints carousel of item pictures
 * @param $pictures
 */
function PrintImageCarousel($pictures) {
	echo <<<_END
	<div class="row">
	<div class="col">
	   <div id="itemImageCarousel" class="carousel slide" data-ride="carousel">
	      <ol class="carousel-indicators">
	         <li data-target="#itemImageCarousel" data-slide-to="0" class="active"></li>
	_END;
	/* Add placeholders on the bottom of the carousel */
	for($i = 1; $i < sizeof($pictures); $i++)
		echo <<<_END
			<li data-target="#itemImageCarousel" data-slide-to="$i"></li>
		_END;
	echo <<<_END
	      </ol>
	      <div class="carousel-inner">
	         <div class="carousel-item active">
	            <img class="d-block w-100" src="{$pictures[0]['directory']}" alt="First slide">
	         </div>
	_END;
	/* Add pictures to carousel */
	for($i = 1, $picture = $pictures[$i]['directory']; $i < count($pictures); $i++, $picture = $pictures[$i]['directory'])
		echo <<<_END
		<div class="carousel-item">
			<img class="d-block w-100" src="$picture" alt="Next Slide">
		</div>
		_END;
	echo <<<_END
	      </div>
	      <a class="carousel-control-prev" href="#itemImageCarousel" role="button" data-slide="prev">
	        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
	        <span class="sr-only">Previous</span>
	      </a>
	      <a class="carousel-control-next" href="#itemImageCarousel" role="button" data-slide="next">
	        <span class="carousel-control-next-icon" aria-hidden="true"></span>
	        <span class="sr-only">Next</span>
	      </a>
	   </div>
	</div>
	_END;
}

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting