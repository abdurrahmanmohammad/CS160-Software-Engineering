<?php

/* Import methods */
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/ItemMethods.php';
require_once './Data Layer/InventoryMethods.php';
require_once './Data Layer/PictureMethods.php';
require_once './Data Layer/CartMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "customer") === 0) header("Location: CustomerPortal.php"); // Customer cannot use admin portal
else if(strcmp($account['accountType'], "admin") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();

/* Get item to view */
$itemID = $_GET['itemID'];
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
    $.get("./nav_admin.html", function(data) {
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