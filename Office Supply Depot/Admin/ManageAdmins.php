<?php
require_once '../Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once '../Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<html>
<head>
<title>Administrator Accounts</title>
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
        <h1>Administrator Accounts</h1>
        <hr>
        <a href="CreateAccount.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Create Account</button>
        </a>
         <a href="AdminPortal.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
</body>
</html>
_END;

if(isset($_POST['delete']) && isset($_POST['email'])) {
	DeleteAccount($conn, get_post($conn, 'email'));
	/* Print confirmation message */
	echo '<div class="alert alert-primary" role="alert">User deleted!</div>';
}


PrintAccounts($conn);


function PrintAccounts($conn) {
	$accounts = SearchAccountByAccountType($conn, "admin");
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
				<td>{$account['account_type']}</td>
				<td>{$account['firstname']}</td>
				<td>{$account['lastname']}</td>
				<td>{$account['phone']}</td>
				<td>{$account['address']}</td>
				<td>
					<form action="UpdateAccount.php" method="post" enctype='multipart/form-data'>
						<input type="hidden" name="email" value="{$account['email']}">
						<button type="submit" class="btn btn-primary" name="update">Update</button>
					</form>
				</td>
			<td>
					<form action="ManageAdmins.php" method="post" enctype='multipart/form-data'>
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

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting