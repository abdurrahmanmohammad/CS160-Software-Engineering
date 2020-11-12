<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate();

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

$OLD_email = get_post($conn, 'OLD_email');
$account = AccountSearch($conn, $OLD_email); // Retrieve account

$prevPage = ($account['accountType'] == "admin") ? "AdministratorAccounts.php" : "CustomerAccounts.php";

echo <<<_END
<html>
<head>
    <title>Update User</title>
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
    <h1>Update User</h1>
    <hr>
_END;


if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['accountType'])
	&& isset($_POST['firstname']) && isset($_POST['lastname'])
	&& isset($_POST['phone']) && isset($_POST['address'])) {
	// Check if password was updated. If so, salt and hash the new password before storing.
	$password = $account['password'] == get_post($conn, 'password') ?
		$account['password'] : password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
	AccountUpdate($conn, $OLD_email, get_post($conn, 'email'), $password,
		get_post($conn, 'accountType'), get_post($conn, 'firstname'), get_post($conn, 'lastname'),
		get_post($conn, 'phone'), get_post($conn, 'address'));
	echo <<<_END
    <div class="alert alert-primary" role="alert">Account updated!</div>
    _END;
	$account = AccountSearch($conn, $OLD_email); // Retrieve updated account
}


echo <<<_END
<form method="POST">
    <div class="form-group">
        <label for="email">Username</label>
        <input type="text" class="form-control" name="email" placeholder="Enter user name"
               value="{$account['email']}" required readonly>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" minlength="8" 
        	maxlength="64" placeholder="Enter password" value="{$account['password']}" required>
    </div>
    <div class="form-group">
        <label for="accountType">Account Type</label>
        <input type="text" class="form-control" name="accountType" placeholder="Account Type"
               value="{$account['accountType']}" required readonly>
    </div>
    <div class="form-group">
        <label for="firstname">First name</label>
        <input type="text" class="form-control" name="firstname" placeholder="Enter first name"
               value="{$account['firstname']}" required>
    </div>
    <div class="form-group">
        <label for="lastname">Last name</label>
        <input type="text" class="form-control" name="lastname" placeholder="Enter last name"
               value="{$account['lastname']}" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" name="phone" placeholder="Enter phone number: (xxx) xxx-xxxx"
               value="{$account['phone']}" required>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <input type="text" class="form-control" name="address" placeholder="Enter address"
               value="{$account['address']}" required>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="$prevPage" class="btn">
            <button type="button" class="btn btn-primary">Back to user list</button>
        </a>
    </div>
    <input type="hidden" name="OLD_email" value="$OLD_email">
</form>
</div>
</body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting