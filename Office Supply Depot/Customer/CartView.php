<?php
require_once '../Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization
require_once '../Data Layer/CartMethods.php';
require_once '../Data Layer/PictureMethods.php';
require_once '../Data Layer/ItemMethods.php';

/** Authenticate user on page */
$account = authenticate('customer'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Print cart page */
echo <<<_END
<head>
    <title>Shopping Cart</title>
	<link rel="stylesheet" href="../CSS/bootstrap.min.css">
	<script src="../JS/jquery-3.2.1.slim.min.js"></script>
	<script src="../JS/popper.min.js"></script>
	<script src="../JS/bootstrap.min.js"></script>
	<script src="../JS/jquery-1.10.2.js"></script>
</head>

<body>
    <div id="nav-placeholder"></div>
       <script>
      $.get("./nav_customer.html", function (data) {
          $("#nav-placeholder").replaceWith(data);
      });
   </script>
    <div class="container">
		<h1>Shopping Cart</h1>
        <hr>
_END;

/** If order placed in Checkout.php, print confirmation */
if(isset($_GET['checkout']) && $_GET['checkout'] == "true")
	echo '<div class="alert alert-primary" role="alert">Order placed!</div>';

/** Update quantity of item in cart */
if(isset($_POST['update'], $account['email'], $_POST['itemID'])) {
	UpdateCart($conn, $account['email'], get_post($conn, 'itemID'), get_post($conn, 'quantity'));
	echo '<div class="alert alert-primary" role="alert">Cart updated!</div>'; // print confirmation message
}

/** Remove item from cart */
if(isset($_POST['remove'], $account['email'], $_POST['itemID'])) {
	DeleteCartItem($conn, $account['email'], get_post($conn, 'itemID'));
	echo '<div class="alert alert-primary" role="alert">Item removed!</div>'; // Print confirmation message
}

/**  Print cart */
$cart = SearchCart($conn, $account['email']); // Retrieve cart from DB
$total = PrintCart($conn, $cart);

/** Retrieve outputs */
$order_total = $total['order_total'];
$order_weight = $total['order_weight'];

if($order_total != '0.00' && $order_weight != '0.00') { // Order cannot be empty
	echo <<<_END
	<form action="Checkout.php" method="post" enctype='multipart/form-data'>
	_END;
	PrintShippingOptions($order_total, $order_weight); // Print shipping options
	echo <<<_END
	<br>
	<input type="hidden" id="total" name="order_total" value="$order_total">
	<input type="hidden" id="total" name="order_weight" value="$order_weight">
	<button type="submit" class="btn btn-primary" id="checkout" name="checkout">Checkout</button>
	</form>
	_END; // Go to checkout page
}

/** Print back button and end of page */
echo <<<_END
	<form action="CustomerPortal.php" method="post" enctype='multipart/form-data'>
		<button type="submit" class="btn btn-primary">Back</button>
	</form>
    </div>
</body>
_END;


function PrintCart($conn, $cart) {
	/** Print head of table */
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

	/** Print items in cart and calculate order info */
	$order_total = 0.0;
	$order_weight = 0.0;
	foreach($cart as $cartItem) {
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$item = SearchItem($conn, $itemID); // Get item info
		$picture = SearchPicture($conn, $itemID)[0]['directory']; // Get item picture
		$quantity = $cartItem['multiplicity'];
		$totalPrice = number_format($item['price'] * $quantity, 2); // Calculate total price
		$totalWeight = number_format($item['weight'] * $quantity, 2); // Calculate total price
		/** Calculate number of items in stock */
		$inventoryA = SearchInventory($conn, $itemID, 'A')['quantity'];
		$inventoryB = SearchInventory($conn, $itemID, 'B')['quantity'];
		$inStock = $inventoryA + $inventoryB; // Total inventory in stock
		/** Update cart total info */
		$order_total += floatval($totalPrice);
		$order_weight += floatval($totalWeight);
		/** Print item info */
		echo <<<_END
		   <tr>
		      <td><img src="$picture" class="rounded" width="200"/></td>
		      <td>{$item['itemID']}</td>
		      <td>{$item['title']}</td>
		      <td>
		        <form action="CartView.php" method="post" enctype='multipart/form-data'>
		            <input type="number" value="$quantity" min="1" max="$inStock" step="1" id="quantity" name="quantity" required>
		            <input type="hidden" id="itemID" name="itemID" value="$itemID">
		            <button type="submit" class="btn btn-primary" id="update" name="update">Update</button>
		        </form>
		      </td>
		      <td>\${$item['price']}</td>
		      <td>\$$totalPrice</td>
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
	/** Print order summary */
	$order_total = number_format($order_total, 2); // Format as a float
	$order_weight = number_format($order_weight, 2); // Format as a float
	echo <<<_END
	<tr>
	   <td>Order Total</td>
	   <td>\$$order_total</td>
	</tr>
	<tr>
	   <td>Order Weight</td>
	   <td>$order_weight</td>
	</tr>
	</tbody>
	</table>
	</div>
	_END;
	/** Return array of order total info */
	return array('order_total' => $order_total, 'order_weight' => $order_weight);
}

/**
 * Print the valid delivery options for an order
 * @param $order_total
 * @param $order_weight
 */
function PrintShippingOptions($order_total, $order_weight) {
	/** Print beginning of element */
	echo <<<_END
	<div id="shipOptions">
		<h1>Shipping Options</h1> 
	_END;

	/** For orders greater than $100 */
	if($order_total > 100.0) {
		/** Option 1: Free (truck) delivery services for any orders over $100.00 (2 day shipping) */
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" name="shipping" value="Option1">
			<input type="hidden" name="shipping_cost" value="0">
			<label class="form-check-label" for="shipping">Free 2-day truck shipping</label>
		</div>
		_END;

		/** Option 2: For same day truck delivery of orders over $100, customer can pay a surcharge of $25 */
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" name="shipping" value="Option2">
			<input type="hidden" name="shipping_cost" value="25">
			<label class="form-check-label" for="Option2">Same day truck shipping ($25)</label>
		</div>
		_END;
	} else {
		/** For any order that are under $100, customer can request deliveries (drone or truck) by paying a surcharge of $20 */
		if($order_weight < 15.0) {
			/** Option 3: For any orders that are less than 15 lbs, the delivery will be done by a drone on the same day during business hours */
			echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" name="shipping" value="Option3" checked>
			<input type="hidden" name="shipping_cost" value="20">
			<label class="form-check-label" for="Option3">Drone 2-day shipping ($20.00)</label>
		</div>
		_END;
		} else {
			/** Option 4: Otherwise the orders will be delivered by delivery truck within 2 business days */
			echo <<<_END
			<div class="form-check">
				<input class="form-check-input" type="radio" name="shipping" value="Option4" checked>
				<input type="hidden" name="shipping_cost" value="20">
				<label class="form-check-label" for="Option4">Truck 2-day shipping ($20.00)</label>
			</div>
		_END;
		}
	}
	/** Print end of element */
	echo '</div>';
}