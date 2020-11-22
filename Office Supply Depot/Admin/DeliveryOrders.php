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
<title>Manage Orders</title>
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
        <h1>Manage Orders</h1>
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
	$new_delivered = ($delivered == "False") ? 1 : 0;
	UpdateDelivered($conn, $orderID, $new_delivered);
}

echo "<h1>Undelivered Orders</h1>";
PrintOrders($conn, 0);

echo "<h1>Delivered Orders</h1>";
PrintOrders($conn, 1);

function PrintOrders($conn, $delivered) {
	/** #################### Option 1 #################### */
	echo <<<_END
	<h4>Option 1: Free truck shipping (2-day)</h4>
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
	$orders = SearchOrdersByShippingAndDelivered($conn, 'Option1', $delivered); // Undelivered orders
	foreach($orders as $order) {
		if(is_null($order['orderID'])) break;
		$delivered = ($order['delivered'] == 1) ? "True" : "False";
		echo <<<_END
		<tr>
			<td>{$order['orderID']}</td>
			<td>{$order['email']}</td>
			<td>{$order['order_weight']}</td>
			<td>{$order['shipping_option']}</td>
			<td>{$order['address']}</td>
			<td>{$order['date_placed']}</td>
			<td>$delivered</td>
			<td>
				<form action="DeliveryOrders.php" method="post" enctype='multipart/form-data'>
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

	/** #################### Option 2 #################### */
	echo <<<_END
	<h4>Option 2: Expedited truck shipping (same day)</h4>
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
	$orders = SearchOrdersByShippingAndDelivered($conn, 'Option2', $delivered); // Undelivered orders
	foreach($orders as $order) {
		if(is_null($order['orderID'])) break;
		$delivered = ($order['delivered'] == 1) ? "True" : "False";
		echo <<<_END
		<tr>
			<td>{$order['orderID']}</td>
			<td>{$order['email']}</td>
			<td>{$order['order_weight']}</td>
			<td>{$order['shipping_option']}</td>
			<td>{$order['address']}</td>
			<td>{$order['date_placed']}</td>
			<td>$delivered</td>
			<td>
				<form action="DeliveryOrders.php" method="post" enctype='multipart/form-data'>
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


	/** #################### Option 3 #################### */
	echo <<<_END
	<h4>Option 3: Paid drone delivery (same day)</h4>
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
	$orders = SearchOrdersByShippingAndDelivered($conn, 'Option3', $delivered); // Undelivered orders
	foreach($orders as $order) {
		if(is_null($order['orderID'])) break;
		$delivered = ($order['delivered'] == 1) ? "True" : "False";
		echo <<<_END
		<tr>
			<td>{$order['orderID']}</td>
			<td>{$order['email']}</td>
			<td>{$order['order_weight']}</td>
			<td>{$order['shipping_option']}</td>
			<td>{$order['address']}</td>
			<td>{$order['date_placed']}</td>
			<td>$delivered</td>
			<td>
				<form action="DeliveryOrders.php" method="post" enctype='multipart/form-data'>
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

	/** #################### Option 4 #################### */
	echo <<<_END
	<h4>Option 4: Regular paid truck shipping (2-day)</h4>
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
	$orders = SearchOrdersByShippingAndDelivered($conn, 'Option4', $delivered); // Undelivered orders
	foreach($orders as $order) {
		if(is_null($order['orderID'])) break;
		$delivered = ($order['delivered'] == 1) ? "True" : "False";
		echo <<<_END
		<tr>
			<td>{$order['orderID']}</td>
			<td>{$order['email']}</td>
			<td>{$order['order_weight']}</td>
			<td>{$order['shipping_option']}</td>
			<td>{$order['address']}</td>
			<td>{$order['date_placed']}</td>
			<td>$delivered</td>
			<td>
				<form action="DeliveryOrders.php" method="post" enctype='multipart/form-data'>
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
	echo <<<_END
		</tbody>
	</table>
	<br><br>
	_END;
}

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting