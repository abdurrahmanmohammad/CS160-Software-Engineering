<?php
require_once '../Data Layer/DatabaseMethods.php';
require_once '../Data Layer/OrderMethods.php';

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>Undelivered Orders</title>
<link rel="stylesheet" href="../CSS/bootstrap.min.css">
<script src="../JS/jquery-3.2.1.slim.min.js"></script>
<script src="../JS/popper.min.js"></script>
<script src="../JS/bootstrap.min.js"></script>
<script src="../JS/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./admin_navbar.html", function(data){
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Undelivered Orders</h1>
        <hr>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
</body>
</html>
_END;

if(isset($_POST['deliver']) && isset($_POST['orderID']) && isset($_POST['delivered'])) {
	$orderID = get_post($conn, 'orderID');
	$delivered = get_post($conn, 'delivered');
	$new_delivered = ($delivered == 0) ? 1 : 0;
	UpdateDelivered($conn, $orderID, $new_delivered);
}

PrintOrders($conn, 1);


function PrintOrders($conn, $delivered) {
	echo '<h4>Option1: Pickup - free</h4>';
	PrintOrderTable($conn, "Option1", $delivered);

	echo '<h4>Option2: Drone Orders >= $100 (2-day) - free</h4>';
	PrintOrderTable($conn, "Option2", $delivered);
	echo '<h4>Option3: Drone Orders >= $100 (same day) - $25</h4>';
	PrintOrderTable($conn, "Option3", $delivered);
	echo '<h4>Option4: Drone Orders < $100 (2-day) - $20</h4>';
	PrintOrderTable($conn, "Option4", $delivered);

	echo '<h4>Option5: Truck Orders >= $100 (2-day) - free</h4>';
	PrintOrderTable($conn, "Option5", $delivered);
	echo '<h4>Option6: Truck Orders >= $100 (same day) - $25</h4>';
	PrintOrderTable($conn, "Option6", $delivered);
	echo '<h4> Option7: Truck orders < $100 (2-day) - $20</h4>';
	PrintOrderTable($conn, "Option7", $delivered);
}

function PrintOrderTable($conn, $option, $delivered) {
	echo <<<_END
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Order ID</th>
				<th>Email</th>
				<th>Order Weight</th>
				<th>Shipping Option</th>
				<th>Address</th>
				<th>Date Placed</th>
				<th>Delivered</th>
				<th>Update Delivery</th>
		   </tr>
		</thead>
		<tbody>
_END;
	$orders = SearchOrdersByShippingAndDelivered($conn, $option, $delivered); // Undelivered orders
	foreach($orders as $order) {
		if(!$order['orderID']) break;
		$delivered_str = ($order['delivered'] == 1) ? "True" : "False";
		echo <<<_END
		<tr>
			<td>{$order['orderID']}</td>
			<td>{$order['email']}</td>
			<td>{$order['order_weight']} (lb.)</td>
			<td>{$order['shipping_option']}</td>
			<td>{$order['address']}</td>
			<td>{$order['date_placed']}</td>
			<td>$delivered_str</td>
			<td>
				<form action="OrdersDelivered.php" method="post" enctype='multipart/form-data'>
		  			<input type="hidden" name="orderID" value="{$order['orderID']}">
		  			<input type="hidden" name="delivered" value="$delivered">
		       		<button type="submit" class="btn btn-primary" id="deliver" name="deliver" value="deliver">Update</button>
				</form>
			</td>
		</tr>
_END;
	}
	echo <<<_END
		</tbody>
	</table>
	<br>
_END;

}


/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting