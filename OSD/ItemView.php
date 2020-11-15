<?php

/* Import methods */
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/ItemMethods.php';
require_once './Data Layer/InventoryMethods.php';
require_once './Data Layer/PictureMethods.php';
require_once './Data Layer/CartMethods.php';

/* Authenticate user on page */
$account = authenticate();

/* Get item to view */
$conn = getConnection(); // Get connection to DB
$itemID = sanitizeMySQL($conn, get_post($conn, 'itemID')); // Extract item ID from previous page
$item = ItemSearchByItemID($conn, $itemID); // Get item from DB
$pictures = PictureSearch($conn, $itemID); // Get item pictures
$inventoryA = InventorySearchByItemID($conn, $itemID, 'A');
$inventoryB = InventorySearchByItemID($conn, $itemID, 'B');
$inStock = $inventoryA['quantity'] + $inventoryB['quantity'];


echo <<<_END
<html>
<head>
    <title>Item view</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>

<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_customer.html", function(data) {
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Item View</h1>
        <hr>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="CustomerPortal.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$item['title']}</li>
            </ol>
        </nav>
_END;

/* Add item to cart */
if(isset($_POST['AddToCart']) && isset($itemID)) { // Check if submit button clicked and if itemID is not null
	$inventoryA = InventorySearchByItemID($conn, $itemID, 'A')['quantity'];
	$inventoryB = InventorySearchByItemID($conn, $itemID, 'B')['quantity'];
	if($inventoryA + $inventoryB != 0) { // If there is stock
		CartInsert($conn, $account['email'], $itemID); // Add item to cart
		echo <<<_END
    <div class="alert alert-primary" role="alert">Item {$item['title']} added to cart!</div>
    _END;
	} else
		echo <<<_END
    <div class="alert alert-primary" role="alert">Out of stock!</div>
    _END;

}

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
                <form action="ItemView.php" method="post">
                	<input type="hidden" id="itemID" name="itemID" value="$itemID">
                	<button type="submit" class="btn btn-primary" id="AddToCart" name="AddToCart">Add to cart</button>
                </form>
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