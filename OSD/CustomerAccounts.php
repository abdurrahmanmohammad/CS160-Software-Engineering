<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate();

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection
echo <<<_END
<html>
<head>
<title>Customer Accounts</title>
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
        <h1>Customer Accounts</h1>
        <hr>
        <a href="AdminCreateAccount.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Create Account</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
</body>
</html>
_END;

if(isset($_POST['delete']) && isset($_POST['email'])) {
	AccountDelete($conn, get_post($conn, 'email'));
	/* Print confirmation message */
	echo <<<_END
	<div class="alert alert-primary" role="alert">User deleted!</div>
	_END;
}


PrintAccounts($conn);


function PrintAccounts($conn) {
	$accounts = AccountSearchType($conn, "customer");
	echo <<<_END
	<table class="table table-bordered">
	<thead>
	   <tr>
	      <th>Username</th>
	      <th>Account Type</th>
	      <th>First name</th>
	      <th>Last name</th>
	      <th>Phone</th>
	      <th>Address</th>
	      <th>Update</th>
	      <th>Delete</th>
	   </tr>
	</thead>
	_END;


	foreach($accounts as $account) {
		echo <<<_END
			<tr>
				<td>{$account['email']}</td>
				<td>{$account['accountType']}</td>
				<td>{$account['firstname']}</td>
				<td>{$account['lastname']}</td>
				<td>{$account['phone']}</td>
				<td>{$account['address']}</td>
				<td>
					<form action="UpdateAccount.php" method="post" enctype='multipart/form-data'>
						<input type="hidden" name="OLD_email" value="{$account['email']}">
						<button type="submit" class="btn btn-primary" name="update">Update</button>
					</form>
				</td>
			<td>
					<form action="CustomerAccounts.php" method="post" enctype='multipart/form-data'>
						<input type="hidden" name="email" value="{$account['email']}">
						<button type="submit" class="btn btn-primary" name="delete">Delete</button>
					</form>
				</td>
			</tr>
		_END;
	}

	echo <<<_END
	</table>
	_END;
}