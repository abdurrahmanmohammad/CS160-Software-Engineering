<?php
/*
For not submitted
	Print page

If form submitted
	Correct input
		Check is user already exists
			Print confirmation message if successfully created
				Go to login page
	Invalid input
		User already exists (same username)
			PK is userID, username, email
		*Password is not secure
		*Address - can be null or anything
		*Phone number is invalid - should e detected by HTML form
		*Email is invalid - should e detected by HTML form

		Inform user of error - JS message
			Print page
*/
require_once './Data Layer/Login.php'; // Import database credentials
require_once './Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once './Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate();

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

$accountExists = false;
$accountCreated = false;

if(isset($_POST['email']) && isset($_POST['password'])
	&& isset($_POST['firstname']) && isset($_POST['lastname'])
	&& isset($_POST['phone']) && isset($_POST['address']) && isset($_POST['accountType'])) {
	$password = password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)

	if(AccountInsert($conn, get_post($conn, 'email'), $password, get_post($conn, 'accountType'),
		get_post($conn, 'firstname'), get_post($conn, 'lastname'),
		get_post($conn, 'phone'), get_post($conn, 'address'))) {
		$accountCreated = true;
		//header("Location: index.php");
	} else $accountExists = true;
	//echo "Account exists!";
}


/**
 * Prints webpage to create user
 */
/*echo <<<_END
<form action="Register.php" method="post" enctype='multipart/form-data'>
<pre>
Register

Email <input type="email" name="email" required>
Password <input type="text" name="password" minlength="8" required>
First name <input type="text" name="firstname" minlength="2" required>
Last name <input type="text" name="lastname" minlength="2" required>
Phone <input type="tel" name="phone" pattern="[0-9]{10)" placeholder="(xxx) xxx-xxxx" required>
Address <input type="text" name="address" required>
<input type="submit" value="Create">

</pre>
</form>
_END;*/

echo <<<_END
<html>
	<head>
		<title>Create Account</title>
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
	        <h1>Create Account</h1>
	    <hr>
_END;
// Print messages
if($accountExists) echo <<<_END
<div class="alert alert-primary" role="alert">Username has been used, please choose another name!</div>
_END;
elseif($accountCreated) echo <<<_END
<div class="alert alert-primary" role = "alert">Account Created!</div >
_END;
echo <<<_END
	    <form method="POST">
	        <div class="form-group">
	            <label for="email">Email</label>
	            <input type="email" class="form-control" name="email" id="email" placeholder="Enter user name" required>
	        </div>
	        <div class="form-group">
	            <label for="password">Password</label>
	            <input type="password" class="form-control" name="password" minlength="8" maxlength="64" placeholder="Enter password" required>
	        </div>
	        <div class="form-group">
	            <label for="firstname">First name</label>
	            <input type="text" class="form-control" name="firstname" minlength="1" placeholder="Enter full name" required>
	        </div>
	        <div class="form-group">
	            <label for="lastname">Last name</label>
	            <input type="text" class="form-control" name="lastname" minlength="1" placeholder="Enter full name" required>
	        </div>
	        <div class="form-group">
	            <label for="phone">Phone</label>
	            <input type="tel" class="form-control" name="phone" pattern="[0-9]{10)" placeholder="(xxx) xxx-xxxx" required>
	        </div>
	        <div class="form-group">
	            <label for="address">Address</label>
	            <input type="text" class="form-control" name="address" placeholder="Enter address">
	        </div>
	        
	        <div class="form-group">
	            <label for="role">Account type</label>
	            <select class="form-control" name="accountType" id="accountType" required>
	                <option value="customer">Customer</option>
	                <option value="admin">Admin</option>
	            </select>
	        </div>
	   		<div class="form-group">
	            <button type="submit" name="create" id="create" class="btn btn-primary">Create</button>
	        </div>
	    </form>
	    </div>
	</body>
</html>
_END;