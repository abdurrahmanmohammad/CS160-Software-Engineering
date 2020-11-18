<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/TransactionsMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/OrderMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PurchasesMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "admin") === 0) header("Location: AdminPortal.php"); // Admin cannot use customer portal
else if(strcmp($account['accountType'], "customer") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>Order History</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.2.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-1.10.2.js"></script>
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

if(isset($_POST['delete']) && isset($_POST['itemID'])) {
	ItemDelete($conn, get_post($conn, 'itemID'), null, null, null, null);
	/* Print confirmation message */
	echo <<<_END
	<div class="alert alert-primary" role="alert">Item deleted!</div>
	_END;
}


$orders = OrdersSearchByEmail($conn, $account['email']); // Retrieve all previous orders
foreach($orders as $order) PrintOrders($conn, $order);


function PrintOrders($conn, $order) {
	$orderID = $order['orderID']; // Get orderID
	$purchases = PurchasesSearch($conn, $orderID);
	$transaction = TransactionsSearch($conn, $orderID);
	$credit_card = "xxxxxxxxxxxx".substr($transaction['credit_card'], -4); // Show only last 4 digits
	echo <<<_END
	<div class="container">
	<table class="table table-bordered">
	<h3>Order: $orderID</h3>
	<thead>
	   <tr>
	      <th>Order Weight</th>
	      <th>Shipping Option</th>
	      <th>Address</th>
	      <th>Date Placed</th>
	   </tr>
	</thead>
	<tbody>
	<tr>
		<td>{$order['order_weight']}</td>
		<td>{$order['shipping_option']}</td>
		<td>{$order['address']}</td>
		<td>{$order['date_placed']}</td>
	</tr>
	</tbody>
	</table>
	_END;


	echo <<<_END
	<table class="table table-bordered">
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
		$item = ItemSearchByItemID($conn, $itemID);
		echo <<<_END
		<tr>
			<td>$itemID</td>
			<td>{$item['title']}</td>
			<td>{$purchase['price']}</td>
			<td>{$purchase['multiplicity']}</td>
		</tr>
		_END;
	}
	echo <<<_END
	</tbody>
	</table>
	_END;


	echo <<<_END
	<table class="table table-bordered">
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
		<td>{$transaction['order_total']}</td>
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