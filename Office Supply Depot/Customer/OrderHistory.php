<?php
require_once '../Data Layer/TransactionMethods.php';
require_once '../Data Layer/OrderMethods.php';
require_once '../Data Layer/PurchaseMethods.php';
require_once '../Data Layer/ItemMethods.php';
require_once '../Data Layer/DatabaseMethods.php';

/** Authenticate user on page */
$account = authenticate('customer'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>Order History</title>
<link rel="stylesheet" href="../CSS/bootstrap.min.css">
<script src="../JS/jquery-3.2.1.slim.min.js"></script>
<script src="../JS/popper.min.js"></script>
<script src="../JS/bootstrap.min.js"></script>
<script src="../JS/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_customer.html", function(data){
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Order History</h1>
        <hr>
         <a href="CustomerPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
</body>
</html>
_END;


$orders = SearchOrdersByEmail($conn, $account['email']); // Retrieve all previous orders
foreach($orders as $order) PrintOrders($conn, $order);


function PrintOrders($conn, $order) {
	$orderID = $order['orderID']; // Get orderID
	$purchases = SearchPurchase($conn, $orderID);
	$transaction = SearchTransaction($conn, $orderID);
	$credit_card = "xxxxxxxxxxxx".substr($transaction['credit_card'], -4); // Show only last 4 digits
	$delivered = ($order['delivered'] == 1) ? "True" : "False";
	echo <<<_END
	<div class="container">
	<table class="table table-bordered">
	<h4>Order Summary</h4>
	<thead>
	   <tr>
	   	  <th>Order ID</th>
	   	  <th>Grand Total</th>
	   	  <th>Order Total</th>
	   	  <th>Shipping Cost</th>
	      <th>Order Weight (lb.)</th>
	      <th>Shipping Option</th>
	      <th>Address</th>
	      <th>Date Placed</th>
	      <th>Delivered</th>
	   </tr>
	</thead>
	<tbody>
	<tr>
		<td>$orderID</td>
		<td>\${$order['grand_total']}</td>
		<td>\${$order['order_total']}</td>
		<td>\${$order['shipping_cost']}</td>
		<td>{$order['order_weight']}</td>
		<td>{$order['shipping_option']}</td>
		<td>{$order['address']}</td>
		<td>{$order['date_placed']}</td>
		<td>$delivered</td>
	</tr>
	</tbody>
	</table>
_END;


	echo <<<_END
	<table class="table table-bordered">
	<h4>Order Items</h4>
	<thead>
	   <tr>
	      <th>Item ID</th>
	      <th>Title</th>
	      <th>Price</th>
	      <th>Quantity</th>
	   </tr>
	</thead>
	<tbody>
_END;
	foreach($purchases as $purchase) {
		$itemID = $purchase['itemID'];
		$item = SearchItem($conn, $itemID);
		echo <<<_END
		<tr>
			<td>$itemID</td>
			<td>{$item['title']}</td>
			<td>\${$purchase['price']}</td>
			<td>{$purchase['multiplicity']}</td>
		</tr>
_END;
	}
	echo <<<_END
	</tbody>
	</table>
_END;

	$order_total = number_format($transaction['order_total'], 2);
	echo <<<_END
	<table class="table table-bordered">
	<h4>Payment Information</h4>
	<thead>
	   <tr>
	      <th>Order Total</th>
	      <th>Card Holder</th>
	      <th>Credit Card</th>
	      <th>Card Month</th>
	      <th>Card Year</th>
	   </tr>
	</thead>
	<tbody>
	<tr>
		<td>\$$order_total</td>
		<td>{$transaction['card_holder']}</td>
		<td>{$credit_card}</td>
		<td>{$transaction['card_month']}</td>
		<td>{$transaction['card_year']}</td>
	</tr>
	</tbody>
	</table>
	</div>
	<hr>
	<br><br><br>
_END;
}
/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting