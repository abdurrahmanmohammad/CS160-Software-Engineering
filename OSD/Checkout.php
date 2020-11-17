<?php
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/CartMethods.php';
require_once './Data Layer/ItemMethods.php';

/** Authenticate user on page */
$account = authenticate();

/** Set up connection to DB */
$conn = getConnection();

/** Calculate order total and order weight */
$orderTotal = 0;
$orderWeight = 0;
$cart = CartSearch($conn, $account['email']);
foreach($cart as $cartItem) {
	// Get itemID and multiplicity of item from item in cart
	$itemID = $cartItem['itemID']; // Get itemID of item in cart
	$quantity = $cartItem['multiplicity'];
	// Retrieve complete item info from DB
	$item = ItemSearchByItemID($conn, $itemID); // Get item info
	// Calculate total price and weight of item
	$orderTotal += number_format($item['price'] * $quantity, 2); // Calculate total price
	$orderWeight += number_format($item['weight'] * $quantity, 2); // Calculate total weight
}

function PrintDeliveryOptions($orderTotal, $orderWeight) {
	echo <<<_END
        <div id="shipOptions">
            <h1 class="display-4">Shipping options</h1>
        
_END;

	if($orderTotal > 100.0) {
		// Option 1: Free delivery services for any orders over $100.00 (2 day shipping)
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="Over1002Day" value="0">
			<label class="form-check-label" for="Over100">Free 2-day truck shipping (free)</label>
		</div>
		_END;

		// Option 2: For same day truck delivery of orders over $100, customer can pay a surcharge of $25
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="Over1001Day" value="25">
			<label class="form-check-label" for="Over100">Same day truck shipping ($25)</label>
		</div>
		_END;
	} else {
		// For any order that are under $100, customer can request deliveries (drone or truck) by paying a surcharge of $20
		if($orderWeight < 15.0) {
			// Option 3: For any orders that are less than 15lbs, the delivery will be done by a drone on the same day during business hours.
			// Drone shipping: $20.00
			echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="Under15" value="20">
			<label class="form-check-label" for="Over100">Drone 2-day shipping ($20.00)</label>
		</div>
		_END;
		}

		// Option 4: Otherwise the orders will be delivered by delivery truck within 2 business days.
		// Truck shipping: $20.00
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="Under15" value="20">
			<label class="form-check-label" for="Over100">Truck 2-day shipping ($20.00)</label>
		</div>
		_END;

	}
	echo <<<_END
</div>
_END;

}


echo <<<_END
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
    $.get("./nav_customer.html", function (data) {
        $("#nav-placeholder").replaceWith(data);
    });
</script>

<div class="container">
    <h1 class="display-3">Check out</h1>
    <hr>
    <div id="shopping-cart">
    <form>
        <div id="shippingAddr">
            <h1 class="display-4">Shipping Address</h1>
            <div class="form-group">
                <label for="inputShippingAddress">Address</label>
                <input type="text" class="form-control" id="inputShippingAddress" placeholder="">
                <script src="//maps.googleapis.com/maps/api/js"></script>
            </div>
            <div class="form-group">
                <label for="inputShippingAddress2">Address 2</label>
                <input type="text" class="form-control" id="inputShippingAddress2"
                       placeholder="Apartment, studio, or floor">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputShippingCity">City</label>
                    <input type="text" class="form-control" id="inputShippingCity">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputShippingState">State</label>
                    <select id="inputShippingState" class="form-control">
                        <option>Choose...</option>
                        <option>CA</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="inputShippingZip">Zip</label>
                    <input type="text" class="form-control" id="inputShippingZip">
                </div>
            </div>
        </div>
        <br>
        <div id="paymentInfo">
            <h1 class="display-4">Payment information</h1>
            <div class="form-group">
                <label for="total">Total</label>
                <input type="text" class="form-control" id="total" placeholder="$orderTotal" readonly>
            </div>
            <div class="form-group">
                <label for="inputCardHolderName">Card Holder Name</label>
                <input type="text" class="form-control" id="inputCardHolderName" placeholder="">
            </div>
            <div class="form-group">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputCardNumber">Card number</label>
                    <input type="text" class="form-control" id="inputCardNumber" placeholder="">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputCVS">CVS</label>
                    <input type="password" class="form-control" id="inputCVS">
                </div>
            </div>
        </div>
        <br>
        <div id="billingAddr">
            <h1 class="display-4">Billing Address</h1>
            <div class="form-group">
                <label for="inputShippingAddress">Address</label>
                <input type="text" class="form-control" id="inputShippingAddress" placeholder="">
            </div>
            <div class="form-group">
                <label for="inputShippingAddress2">Address 2</label>
                <input type="text" class="form-control" id="inputShippingAddress2"
                       placeholder="Apartment, studio, or floor">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputShippingCity">City</label>
                    <input type="text" class="form-control" id="inputShippingCity">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputShippingState">State</label>
                    <select id="inputShippingState" class="form-control">
                        <option>Choose...</option>
                        <option>CA</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="inputShippingZip">Zip</label>
                    <input type="text" class="form-control" id="inputShippingZip">
                </div>
            </div>
        </div>
        <br>
_END;

        
PrintDeliveryOptions($orderTotal, $orderWeight);
echo <<<_END
        <br>
        <button type="submit" class="btn btn-primary">Place Order</button>
    </form>
    <br>
</div>
</body>
</html>
_END;


// Key: AIzaSyBhIsVW0TpV98OnkzmnuFgRSjTDQkNoZfg