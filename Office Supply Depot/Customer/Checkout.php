<?php
require_once '../Data Layer/DatabaseMethods.php';
require_once '../Data Layer/CartMethods.php';
require_once '../Data Layer/ItemMethods.php';
require_once '../Data Layer/InventoryMethods.php';
require_once '../Data Layer/TransactionMethods.php';
require_once '../Data Layer/OrderMethods.php';
require_once '../Data Layer/PurchaseMethods.php';
require_once '../Data Layer/CartMethods.php';

/** Authenticate user on page */
$account = authenticate('customer'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Print page */
echo <<<_END
<html>
   <head>
      <title>Checkout</title>
      <link rel="stylesheet" href="../CSS/bootstrap.min.css">
      <script src="../JS/jquery-3.2.1.slim.min.js"></script>
      <script src="../JS/popper.min.js"></script>
      <script src="../JS/bootstrap.min.js"></script>
      <script src="../JS/jquery-1.10.2.js"></script>
      <!-- Start: Google Maps API -->
      <link rel="stylesheet" href="../CSS/GoogleMapsAPI.css">
      <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBHBlsmFRD1tJK4kU8qTl9cATAtEyxXF7A&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
      <script src="../JS/GoogleMapsAPI.js"></script>
      <!-- End: Google Maps API -->
   </head>
<body>
   <div id="nav-placeholder"></div>
   <script>
      $.get("./nav_customer.html", function (data) {
          $("#nav-placeholder").replaceWith(data);
      });
   </script>
   <div class="container">
   <h1 class="display-3">Checkout</h1>
   <hr>
_END;

/** Get variables from cart page */
$order_total = floatval(get_post($conn, 'order_total')); // Get order total
$order_weight = floatval(get_post($conn, 'order_weight')); // Get order weight
$shipping_cost = floatval(get_post($conn, 'shipping_cost')); // Get shipping cost
$shipping = get_post($conn, 'shipping'); // Get shipping option
$grand_total = $order_total + $shipping_cost; // Calculate grand total

/** If any variables are null or 0 */
if($order_total <= 0 || $order_weight <= 0 || $shipping_cost <= 0)
	header("Location: CartView.php"); // Return to cart

/** Retrieve cart */
$cart = SearchCart($conn, $account['email']);

/** If order was placed (button was clicked) */
if(isset($_POST['street_number'], $_POST['route'], $_POST['locality'], $_POST['administrative_area_level_1'],
	$_POST['postal_code'], $_POST['country'], $_POST['OrderTotal'], $_POST['CardHolderName'], $_POST['CardNumber'],
	$_POST['Month'], $_POST['Year'], $_POST['CVC'], $_POST['shipping'], $_POST['order_weight'])) {
	ExecuteOrder($conn, $cart, $account['email'], $grand_total, $order_total, $shipping_cost, $order_weight, $shipping);
}

/** Print order summary: print items in cart and cart total */
PrintOrderSummary($conn, $cart, $order_total, $order_weight, $shipping_cost, $grand_total);

/** Print checkout form */
echo <<<_END
<div id="shopping-cart">
   <form action="Checkout.php" method="post" enctype='multipart/form-data'>
   		<input type="hidden" name="order_total" value="$order_total">
   		<input type="hidden" name="order_weight" value="$order_weight">
   		<input type="hidden" name="shipping_cost" value="$shipping_cost">
   		<input type="hidden" name="shipping" value="$shipping">
      <!-- Start: Google Maps API -->
      <h1>Shipping Address</h1>
      <div class="shippingAddr" id="locationField">
         <div class="form-row">
            <input class="form-control" id="autocomplete" placeholder="Enter your address" onFocus="geolocate()" type="text"/>
            <br><br>
         </div>
         <div class="form-row">
            <div class="form-group col-md-6">
               <label for="street_number">Street Number</label>
               <input class="field" id="street_number" name="street_number" disabled="true" readonly required>
            </div>
            <div class="form-group col-md-6">
               <label for="route">Street Name</label>
               <input class="field" id="route" name="route" disabled="true" readonly required>
            </div>
         </div>
         <div class="form-row">
            <div class="form-group col-md-6">
               <label for="locality">City</label>
               <input class="field" id="locality" name="locality" disabled="true" readonly required>
            </div>
            <div class="form-group col-md-4">
               <label for="administrative_area_level_1">State</label>
               <input class="field" id="administrative_area_level_1" name="administrative_area_level_1" disabled="true" readonly required>
            </div>
            <div class="form-group col-md-2">
               <label for="postal_code">Zip</label>
               <input class="field" id="postal_code" name="postal_code" disabled="true" readonly required>
            </div>
            <div class="form-group col-md-4">
               <label for="country">Country</label>
               <input class="field" id="country" name="country" disabled="true" readonly required>
            </div>
         </div>
         <br>
         <div id="paymentInfo">
            <h1>Payment information</h1>
            <div class="form-group">
               <label for="total">Order Total (\$)</label>
               <input type="text" class="form-control" id="OrderTotal" name="OrderTotal" value="$order_total" readonly>
            </div>
            <div class="form-group">
               <label for="CardHolderName">Card Holder Name</label>
               <input type="text" class="form-control" id="CardHolderName" name="CardHolderName" placeholder="Enter name" required>
            </div>
            <div class="form-group">
            </div>
            <div class="form-row">
               <div class="form-group col-md-6">
                  <label for="inputCardNumber">Card number</label>
                  <input type="text" class="form-control" id="CardNumber" name="CardNumber" placeholder="Card Number" pattern="\d*" minlength="16" maxlength="16" required>
               </div>
            </div>
            <div class="form-row">
               <div class="form-group col-md-3">
                  <label for="inputCardNumber">Expires Month</label>
                  <input type="tel" class="form-control" id="Month" name="Month" pattern="[0-9]{2}" placeholder="MM" required>
               </div>
               <div class="form-group col-md-3">
                  <label for="inputCardNumber">Expires Year</label>
                  <input type="tel" class="form-control" id="Year" name="Year" pattern="[0-9]{2}" placeholder="MM" required>
               </div>
               <div class="form-group col-md-3">
                  <label for="inputCVS">CVC</label>
                  <input type="text" class="form-control" id="CVC" name="CVC" pattern="\d*" minlength="3" maxlength="3" placeholder="CVC" required>
               </div>
            </div>
         </div>
         <button type="submit" class="btn btn-primary">Place Order</button>
      </div>
   </form>
</div>
</body>
_END;


/**
 * Print order summary
 * @param $conn
 * @param $cart
 * @param $order_total
 * @param $order_weight
 * @param $shipping_cost
 * @param $grand_total
 * @return array|void
 */
function PrintOrderSummary($conn, $cart, $order_total, $order_weight, $shipping_cost, $grand_total) {
	/** Print beginning of table */
	echo <<<_END
	<div id="shopping-cart">
	<h1>Order Summary</h1>
	<table class="table">
	   <tbody>
	   <thead>
	      <tr>
	         <th>Item ID</th>
	         <th>Title</th>
	         <th>Quantity</th>
	         <th>Unit Price</th>
	         <th>Total Price</th>
	         <th>Weight</th>
	      </tr>
	   </thead>
	_END;

	/** Print each item in cart */
	foreach($cart as $cartItem) {
		/** Get itemID and multiplicity of item from item in cart */
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$quantity = $cartItem['multiplicity']; // Get multiplicity of item in cart

		/** Retrieve complete item info from DB */
		$item = SearchItem($conn, $itemID); // Get item info

		/** Calculate total price and weight of item */
		$item_total = number_format($item['price'] * $quantity, 2); // Calculate total price

		/** Print item details */
		echo <<<_END
		   <tr>
		      <td>$itemID</td>
		      <td>{$item['title']}</td>
		      <td>{$cartItem['multiplicity']}</td>
		      <td>{$item['price']}</td>
		      <td>$item_total</td>
		      <td>{$item['weight']}</td>
		   </tr>
		_END;
	}

	/** Print order summary */
	echo <<<_END
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><h5>Weight</h5></td>
			<td><h6>$order_weight</h6></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<tr>
			<td><h5>Order Total</h5></td>
			<td><h6>\$$order_total</h6></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<td><h5>Shipping</h5></td>
		<td><t5>\$$shipping_cost</t5></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><h5>Grand Total</h5></td>
			<td><h6>\$$grand_total</h6></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	   </tbody>
	</table>
	</div>
	_END;
}


function ExecuteOrder($conn, $cart, $email, $grand_total, $order_total, $shipping_cost, $order_weight, $shipping) {
	/** ########## Step 1: Extract form inputs ########## */
	/** Get shipping address */
	$street_number = get_post($conn, 'street_number');
	$street_name = get_post($conn, 'route');
	$city = get_post($conn, 'locality');
	$state = get_post($conn, 'administrative_area_level_1');
	$zip = get_post($conn, 'postal_code');
	$country = get_post($conn, 'country');
	$address = $street_number." ".$street_name." ".$city." ".$state." ".$zip." ".$country; // Concatenate to get address

	/** Get payment information */
	$card_holder = get_post($conn, 'CardHolderName');
	$credit_card = get_post($conn, 'CardNumber');
	$card_month = get_post($conn, 'Month');
	$card_year = get_post($conn, 'Year');
	$cvc = get_post($conn, 'CVC');

	/** Get order information */
	$OrderTotal = number_format(get_post($conn, 'OrderTotal'), 2);
	$OrderWeight = get_post($conn, 'order_weight');
	$shipping = get_post($conn, 'shipping');


	/** ########## Step 2: Validate inputs ########## */
	/** Validate credit card */
	/* If same year: check if months is valid */
	if(intval(date("Y")) === intval("20".$card_year) && intval($card_month) <= date("m")) {
		// If same year but credit card's month is less than or equal to current month
		echo <<<_END
		<div class="alert alert-primary" role="alert">Expired card! Month out of range!</div>
		_END; // Card month must be greater than current month
		return false;
	} else if(intval("20".$card_year) < intval(date("Y"))) { // If different years
		// If different years but credit card's year is before current year
		echo <<<_END
		<div class="alert alert-primary" role="alert">Expired card! Year out of range! </div>
		_END; // Card month must be greater than current month
		return false;
	}

	/** Validate address: Must be located in San Jose, CA, USA */
	if(strcmp($country, 'United States') !== 0) { // Must be in the US
		echo <<<_END
		<div class="alert alert-primary" role="alert">Must be in the United States!</div>
		_END; // Card month must be greater than current month
		return false;
	} else if(strcmp($state, 'CA') !== 0) { // Must be in the CA
		echo <<<_END
		<div class="alert alert-primary" role="alert">Must be in the California!</div>
		_END; // Card month must be greater than current month
		return false;
	} else if(strcmp($city, 'San Jose') !== 0) { // // Must be in the SJ
		echo <<<_END
		<div class="alert alert-primary" role="alert">Must be in San Jose!</div>
		_END; // Card month must be greater than current month
		return false;
	}

	/** ########## Step 3: Make sure we have enough inventory to complete order ########## */
	foreach($cart as $cartItem) {
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$quantity = $cartItem['multiplicity']; // Get multiplicity of item in cart

		/** Retrieve complete item info from DB */
		$item = SearchItem($conn, $itemID); // Get item info

		/** Get inventory info */
		$inventoryA = SearchInventory($conn, $itemID, 'A')['quantity'];
		$inventoryB = SearchInventory($conn, $itemID, 'B')['quantity'];
		$totalInventory = $inventoryA + $inventoryB; // Calculate total inventory

		// Check if order can be placed with current inventory state
		if($totalInventory === 0) { // If inventory for an item is out of stock
			echo <<<_END
			<div class="alert alert-primary" role="alert">Out of stock!</div>
			_END;
			return false;
		} else if($totalInventory < $quantity) { // If quantity exceeds stock
			echo <<<_END
			<div class="alert alert-primary" role="alert">Quantity of {$item['title']} exceeds stock!</div>
			_END;
			return false;
		}
	}

	/** ########## Step 4: Place the order ########## */
	/** Create a new order */
	$orderID = GenerateOrderID($conn); // Create a new orderID
	InsertOrder($conn, $orderID, $email, $grand_total, $order_total, $shipping_cost, $order_weight, $shipping, $address);

	/** Create a new payment transaction */
	InsertTransaction($conn, $orderID, $OrderTotal, $card_holder, $credit_card, $card_month, $card_year, $cvc);

	foreach($cart as $cartItem) {
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$quantity = $cartItem['multiplicity']; // Get multiplicity of item in cart

		/** Retrieve complete item info from DB */
		$item = SearchItem($conn, $itemID); // Get item info

		/** Get inventory info */
		$inventoryA = SearchInventory($conn, $itemID, 'A')['quantity'];
		$inventoryB = SearchInventory($conn, $itemID, 'B')['quantity'];
		$totalInventory = $inventoryA + $inventoryB; // Calculate total inventory

		/** Update inventory for transaction */
		if($inventoryA >= $quantity) { // If inventory A has enough stock
			UpdateInventoryQuantity($conn, $itemID, 'A', $inventoryA - $quantity);
		} else if($inventoryB >= $quantity) { // If inventory B has enough stock
			UpdateInventoryQuantity($conn, $itemID, 'B', $inventoryB - $quantity);
		} else { // if($totalInventory - $quantity >= 0) // There is enough stock in inventory A + B
			UpdateInventoryQuantity($conn, $itemID, 'A', 0); // Empty inventory A
			$totalInventory -= $inventoryA; // Figure out how much we have to subtract from inventory B
			UpdateInventoryQuantity($conn, $itemID, 'B', $inventoryB - $totalInventory); // Subtract remainder stock from inventory B
		}
		/** Add item to customer's purchase history */
		InsertPurchase($conn, $orderID, $itemID, $item['price'], $quantity); // Transfer items from cart to purchase history
	}

	/** Empty cart */
	DeleteCart($conn, $email); // Empty cart after checkout
	header("Location: CartView.php?checkout=true"); // Return to cart
}

