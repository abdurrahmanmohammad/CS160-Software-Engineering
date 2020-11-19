<?php
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/CartMethods.php';
require_once './Data Layer/ItemMethods.php';
require_once './Data Layer/InventoryMethods.php';
require_once './Data Layer/TransactionsMethods.php';
require_once './Data Layer/OrderMethods.php';
require_once './Data Layer/PurchasesMethods.php';
require_once './Data Layer/CartMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "admin") === 0) header("Location: AdminPortal.php"); // Admin cannot use customer portal
else if(strcmp($account['accountType'], "customer") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();

/** Get shipping */
$shipping = 0.0; // Initially 0 (even if no option was selected)
if(isset($_POST['shipping'])) $shipping = GetShipping($conn);

/** Retrieve cart */
$cart = CartSearch($conn, $account['email']);


if(isset($_POST['street_number'], $_POST['route'], $_POST['locality'], $_POST['administrative_area_level_1'],
	$_POST['postal_code'], $_POST['country'], $_POST['OrderTotal'], $_POST['CardHolderName'], $_POST['CardNumber'],
	$_POST['Month'], $_POST['Year'], $_POST['CVC'], $_POST['shipping'], $_POST['OrderWeight'])) {
	ExecuteOrder($conn, $cart, $account['email']);

}


echo <<<_END
<html>
<head>
    <title>Checkout</title>
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
    
    <!-- Start: Google Maps API -->
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBHBlsmFRD1tJK4kU8qTl9cATAtEyxXF7A&callback=initAutocomplete&libraries=places&v=weekly"
            defer
    ></script>
    <style type="text/css">
        /* Always set the map height explicitly to define the size of the div
		 * element that contains the map. */
        #map {
            height: 100%;
        }

        /* Optional: Makes the sample page fill the window. */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #locationField,
        #controls {
            position: relative;
            width: 480px;
        }

        #autocomplete {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 99%;
        }

        .label {
            text-align: right;
            font-weight: bold;
            width: 100px;
            color: #303030;
            font-family: "Roboto", Arial, Helvetica, sans-serif;
        }

        #address {
            border: 1px solid #000090;
            background-color: #f0f9ff;
            width: 480px;
            padding-right: 2px;
        }

        #address td {
            font-size: 10pt;
        }

        .field {
            width: 99%;
        }

        .slimField {
            width: 80px;
        }

        .wideField {
            width: 200px;
        }

        #locationField {
            height: 20px;
            margin-bottom: 2px;
        }
    </style>
    <script>
        // This sample uses the Autocomplete widget to help the user select a
        // place, then it retrieves the address components associated with that
        // place, and then it populates the form fields with those details.
        // This sample requires the Places library. Include the libraries=places
        // parameter when you first load the API. For example:
        // <script
        // src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
        let placeSearch;
        let autocomplete;
        const componentForm = {
            street_number: "short_name",
            route: "long_name",
            locality: "long_name",
            administrative_area_level_1: "short_name",
            country: "long_name",
            postal_code: "short_name",
        };

        function initAutocomplete() {
            // Create the autocomplete object, restricting the search predictions to
            // geographical location types.
            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("autocomplete"),
                {types: ["geocode"]}
            );
            // Avoid paying for data that you don't need by restricting the set of
            // place fields that are returned to just the address components.
            autocomplete.setFields(["address_component"]);
            // When the user selects an address from the drop-down, populate the
            // address fields in the form.
            autocomplete.addListener("place_changed", fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            const place = autocomplete.getPlace();

            for (const component in componentForm) {
                document.getElementById(component).value = "";
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details,
            // and then fill-in the corresponding field on the form.
            for (const component of place.address_components) {
                const addressType = component.types[0];

                if (componentForm[addressType]) {
                    const val = component[componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }
        }

        // Bias the autocomplete object to the user's geographical location,
        // as supplied by the browser's 'navigator.geolocation' object.
        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    const geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    const circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy,
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }
    </script>
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
    <h1 class="display-3">Check out</h1>
    <hr>
_END;


/** Print order summary: print items in cart and cart total */
$order = PrintOrderSummary($conn, $cart, $shipping);
$orderTotal = $order[0];
$orderWeight = $order[1];

if($orderTotal <= 0.0) header("Location: CartView.php"); // Return to cart

echo <<<_END
    <div id="shopping-cart">
    <form action="Checkout.php" method="post" enctype='multipart/form-data'>
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
      <input type="text" class="form-control" id="OrderTotal" name="OrderTotal" value="$orderTotal" readonly>
   		<input type="hidden" id="OrderWeight" name="OrderWeight" value="$orderWeight">
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
         <input type="number" step="1" min="1" max="12" class="form-control" id="Month" name="Month" required>
      </div>
      <div class="form-group col-md-3">
         <label for="inputCardNumber">Expires Year</label>
         <input type="number" step="1" min="20" max="50" class="form-control" id="Year" name="Year" required>
      </div>
      <div class="form-group col-md-3">
         <label for="inputCVS">CVC</label>
         <input type="text" class="form-control" id="CVC" name="CVC" pattern="\d*" minlength="3" maxlength="3" required>
      </div>
   </div>
</div>
<br>
_END;
/** Print the valid delivery options for an order */
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

/**
 * Print the valid delivery options for an order
 * @param $orderTotal
 * @param $orderWeight
 */
function PrintDeliveryOptions($orderTotal, $orderWeight) {
	/** Print beginning of element */
	echo <<<_END
	<div id="shipOptions">
		<h1 class="display-4">Shipping options</h1> 
	_END;

	/** For orders greater than $100 */
	if($orderTotal > 100.0) {
		/** Option 1: Free (truck) delivery services for any orders over $100.00 (2 day shipping) */
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="shipping" name="shipping" value="Option1">
			<label class="form-check-label" for="shipping">Free 2-day truck shipping</label>
		</div>
		_END;

		/** Option 2: For same day truck delivery of orders over $100, customer can pay a surcharge of $25 */
		echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="shipping" name="shipping" value="Option2">
			<label class="form-check-label" for="Option2">Same day truck shipping ($25)</label>
		</div>
		_END;
	} else {
		/** For any order that are under $100, customer can request deliveries (drone or truck) by paying a surcharge of $20 */
		if($orderWeight < 15.0) {
			/** Option 3: For any orders that are less than 15 lbs, the delivery will be done by a drone on the same day during business hours */
			echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="shipping" name="shipping" value="Option3" checked>
			<label class="form-check-label" for="Option3">Drone 2-day shipping ($20.00)</label>
		</div>
		_END;
		} else {
			/** Option 4: Otherwise the orders will be delivered by delivery truck within 2 business days */
			echo <<<_END
		<div class="form-check">
			<input class="form-check-input" type="radio" id="shipping" name="shipping" value="Option4" checked>
			<label class="form-check-label" for="Option4">Truck 2-day shipping ($20.00)</label>
		</div>
		_END;
		}
	}

	/** Print end of element */
	echo <<<_END
	</div>
	_END;
}

/**
 * Print order summary
 * @param $conn
 * @param $cart
 * @param $shipping
 * @return array|void
 */
function PrintOrderSummary($conn, $cart, $shipping) {
	/** Check inputs */
	if(is_null($conn) || is_null($cart)) return;

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

	/** Print items in cart */
	$orderTotal = 0.0;
	$orderWeight = 0.0;

	foreach($cart as $cartItem) {
		/** Get itemID and multiplicity of item from item in cart */
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$quantity = $cartItem['multiplicity']; // Get multiplicity of item in cart

		/** Retrieve complete item info from DB */
		$item = ItemSearchByItemID($conn, $itemID); // Get item info

		/** Calculate total price and weight of item */
		$itemTotal = number_format($item['price'] * $quantity, 2); // Calculate total price
		$itemWeight = number_format($item['weight'] * $quantity, 2); // Calculate total weight

		/** Update order total price and weight */
		$orderTotal += $itemTotal;
		$orderWeight += $itemWeight;

		/** Print item details */
		echo <<<_END
		   <tr>
		      <td>$itemID</td>
		      <td>{$item['title']}</td>
		      <td>{$cartItem['multiplicity']}</td>
		      <td>{$item['price']}</td>
		      <td>$itemTotal</td>
		      <td>{$item['weight']}</td>
		   </tr>
		_END;
	}

	/** Print end of table */

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
			<td><h6>$orderWeight</h6></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><h5>Shipping</h5></td>
		<td><t5>\$$shipping</t5></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><h5>Total</h5></td>
			<td><h6>$orderTotal</h6></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	   </tbody>
	</table>
	</div>
	_END;
	/** Store and return outputs */
	$order = array();
	$order[0] = $orderTotal;
	$order[1] = $orderWeight;
	return $order;
}

function GetShipping($conn) {
	// Step 1: Check which shipping option was clicked
	// Step 2: Add shipping cost to total
	$shipping = 0.0;
	$option = get_post($conn, 'shipping');
	switch($option) {
		// Orders over $100
		case "Option1":
			$shipping = 0; // Free delivery services for any orders over $100.00 (2 day shipping)
			break;
		case "Option2":
			$shipping = 25;// For same day truck delivery of orders over $100, customer can pay a surcharge of $25
			break;
		// For any order that are under $100, customer can request deliveries (drone or truck) by paying a surcharge of $20
		case "Option3":
			$shipping = 20;// For any orders that are less than 15 lbs, the delivery will be done by a drone on the same day during business hours
			break;
		case "Option4":
			$shipping = 20; // Otherwise the orders will be delivered by delivery truck within 2 business days
			break;
		default: // Invalid shipping option
			echo <<<_END
			<div class="alert alert-primary" role="alert">Invalid shipping option!</div>
			_END;
	}
	return $shipping;
}

function ExecuteOrder($conn, $cart, $email) {

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
	$OrderWeight = get_post($conn, 'OrderWeight');
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
		$item = ItemSearchByItemID($conn, $itemID); // Get item info

		/** Get inventory info */
		$inventoryA = InventorySearchByItemID($conn, $itemID, 'A')['quantity'];
		$inventoryB = InventorySearchByItemID($conn, $itemID, 'B')['quantity'];
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
	OrdersInsert($conn, $orderID, $email, $OrderWeight, $shipping, $address);

	/** Create a new payment transaction */
	TransactionsInsert($conn, $orderID, $OrderTotal, $card_holder, $credit_card, $card_month, $card_year, $cvc);

	foreach($cart as $cartItem) {
		$itemID = $cartItem['itemID']; // Get itemID of item in cart
		$quantity = $cartItem['multiplicity']; // Get multiplicity of item in cart

		/** Retrieve complete item info from DB */
		$item = ItemSearchByItemID($conn, $itemID); // Get item info

		/** Get inventory info */
		$inventoryA = InventorySearchByItemID($conn, $itemID, 'A')['quantity'];
		$inventoryB = InventorySearchByItemID($conn, $itemID, 'B')['quantity'];
		$totalInventory = $inventoryA + $inventoryB; // Calculate total inventory

		/** Update inventory for transaction */
		if($inventoryA >= $quantity) { // If inventory A has enough stock
			InventoryUpdateQuantity($conn, $itemID, 'A', $inventoryA - $quantity);
		} else if($inventoryB >= $quantity) { // If inventory B has enough stock
			InventoryUpdateQuantity($conn, $itemID, 'B', $inventoryB - $quantity);
		} else { // if($totalInventory - $quantity >= 0) // There is enough stock in inventory A + B
			InventoryUpdateQuantity($conn, $itemID, 'A', 0); // Empty inventory A
			$totalInventory -= $inventoryA; // Figure out how much we have to subtract from inventory B
			InventoryUpdateQuantity($conn, $itemID, 'B', $inventoryB - $totalInventory); // Subtract remainder stock from inventory B
		}
		/** Add item to customer's purchase history */
		PurchasesInsert($conn, $orderID, $itemID, $item['price'], $quantity); // Transfer items from cart to purchase history
	}

	/** Empty cart */
	CartDeleteAll($conn, $email); // Empty cart after checkout
	header("Location: CartView.php?checkout=true"); // Return to cart
}

