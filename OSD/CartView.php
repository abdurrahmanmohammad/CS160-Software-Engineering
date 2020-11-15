<?php
/*
 *  Cart view - cart icon on homepage navbar,
    when clicked go to cart page which display all items in cart,
    have select shipping list
 */
require_once './Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization
require_once './Data Layer/CartMethods.php';
require_once './Data Layer/PictureMethods.php';
require_once './Data Layer/ItemMethods.php';

/** Authenticate user on page */
$account = authenticate();

/** Set up connection to DB */
$conn = getConnection();


echo <<<_END

<!DOCTYPE html>
<html>

<head>
    <title>Shopping Cart</title>
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
        <h1>Shopping Cart</h1>
        <hr>
_END;


if(isset($_POST['update']) && isset($account['email']) && isset($_POST['itemID'])) {
	CartUpdate($conn, $account['email'], get_post($conn, 'itemID'), get_post($conn, 'quantity'));
	/* Print confirmation message */
	echo <<<_END
	<div class="alert alert-primary" role="alert">Cart updated!</div>
	_END;
}

if(isset($_POST['remove']) && isset($account['email']) && isset($_POST['itemID'])) {
	CartDelete($conn, $account['email'], get_post($conn, 'itemID'));
	/* Print confirmation message */
	echo <<<_END
	<div class="alert alert-primary" role="alert">Item removed!</div>
	_END;
}

$cart = CartSearch($conn, $account['email']);
PrintCart($conn, $cart);

function PrintCart($conn, $cart) {
	echo <<<_END
	<div id="shopping-cart">
	<table class="table">
	   <tbody>
	   <thead>
	      <tr>
	         <th>Picture</th>
	         <th>Item ID</th>
	         <th>Title</th>
	         <th>Quantity</th>
	         <th>Unit Price</th>
	         <th>Total Price</th>
	         <th>Weight</th>
	         <th>Description</th>
	         <th>Remove</th>
	      </tr>
	   </thead>
	_END;

	/* Print items in cart */
	$total = 0.0;
	foreach($cart as $cartItem) {
		if(is_null($cartItem[0])) {

			break;
		}
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$item = ItemSearchByItemID($conn, $itemID); // Get item info
		$picture = PictureSearch($conn, $itemID)[0]['directory']; // Get item picture
		$quantity = $cartItem['multiplicity'];
		$totalPrice = number_format($item['price'] * $quantity, 2); // Calculate total price
		/* Calculate number of items in stock */
		$inventoryA = InventorySearchByItemID($conn, $itemID, 'A')['quantity'];
		$inventoryB = InventorySearchByItemID($conn, $itemID, 'B')['quantity'];
		$inStock = $inventoryA + $inventoryB;
		/* Update cart total */
		$total += $item['price'];
		echo <<<_END
		   <tr>
		      <td><img src="$picture" class="rounded" width="200"/></td>
		      <td>{$item['itemID']}</td>
		      <td>{$item['title']}</td>
		      <td>
		        <form action="CartView.php" method="post" enctype='multipart/form-data'>
		            <input type="number" value="$quantity" min="0" max="$inStock" step="1" id="quantity" name="quantity" required>
		            <input type="hidden" id="itemID" name="itemID" value="$itemID">
		            <button type="submit" class="btn btn-primary" id="update" name="update">Update</button>
		        </form>
		      </td>
		      <td>{$item['price']}</td>
		      <td>$totalPrice</td>
		      <td>{$item['weight']}</td>
		      <td>{$item['description']}</td>
		      <td>
		        <form action="CartView.php" method="post" enctype='multipart/form-data'>
		            <input type="hidden" name="itemID" value="$itemID">
		            <button type="submit" class="btn btn-primary" id="remove" name="remove">Remove</button>
		        </form>
		      </td>
		   </tr>
		_END;
	}
	echo <<<_END
   </tbody>
</table>
</div>
_END;

}

if(isset($cart[0][0]))
	echo <<<_END
	<form action="Checkout.php" method="post" enctype='multipart/form-data'>
		<button type="submit" class="btn btn-primary" id="checkout" name="checkout">Checkout</button>
	</form>
_END;

echo <<<_END
	<br>
	<form action="CustomerPortal.php" method="post" enctype='multipart/form-data'>
		<button type="submit" class="btn btn-primary" name="update">Back</button>
	</form>
    </div>
</body>

</html>
_END;
