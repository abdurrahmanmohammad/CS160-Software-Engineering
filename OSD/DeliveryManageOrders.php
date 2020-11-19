<?php
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/OrderMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "customer") === 0) header("Location: CustomerPortal.php"); // Customer cannot use admin portal
else if(strcmp($account['accountType'], "admin") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>Manage Orders</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.2.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_admin.html", function(data){
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
	OrderDeliver($conn, $orderID, $new_delivered);
}

PrintOrders($conn);

function PrintOrders($conn) {
	/** #################### Option 1 #################### */
	echo <<<_END
	<h3>Option 1: Free truck shipping (2-day)</h3>
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
	$orders = OrdersGetUndelivered($conn, 'Option1');
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
				<form action="DeliveryManageOrders.php" method="post" enctype='multipart/form-data'>
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
	<br><br>
	_END;

	/** #################### Option 2 #################### */
	echo <<<_END
	<h3>Option 2: Expedited truck shipping (same day)</h3>
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
	$orders = OrdersGetUndelivered($conn, 'Option2');
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
				<form action="DeliveryManageOrders.php" method="post" enctype='multipart/form-data'>
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
	<br><br>
	_END;


	/** #################### Option 3 #################### */
	echo <<<_END
	<h3>Option 3: Paid drone delivery (same day)</h3>
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
	$orders = OrdersGetUndelivered($conn, 'Option3');
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
				<form action="DeliveryManageOrders.php" method="post" enctype='multipart/form-data'>
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
	<br><br>
	_END;

	/** #################### Option 4 #################### */
	echo <<<_END
	<h3>Option 4: Regular paid truck shipping (2-day)</h3>
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
	$orders = OrdersGetUndelivered($conn, 'Option4');
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
				<form action="DeliveryManageOrders.php" method="post" enctype='multipart/form-data'>
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
	<br><br>
	_END;

	/** #################### Delivered Orders #################### */
	echo <<<_END
	<h3>Delivered Orders</h3>
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
	$orders = OrdersGetDelivered($conn);
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
				<form action="DeliveryManageOrders.php" method="post" enctype='multipart/form-data'>
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
	<br><br>
	_END;


}