<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Directory of file
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

if(isset($_POST['email']) && isset($_POST['password'])) {
	if(!CheckPassword($conn))
		echo "Invalid username/password combination"; // Replace with JS message
}


/** Print login page */
echo <<< _END
<html>
<head>
<title>Sign In</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/main.css">
<script src="js/jquery-3.2.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-1.10.2.js"></script>
</head>
<body class="text-center">
    <form class="form-signin" action="Login.php" method="post">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
    </form>
</body>
</html>
_END;

/**
 * Retrieves inputted email and password and verifies account and password
 * @param $conn
 * @return bool
 */
function CheckPassword($conn) {
	/* Retrieve inputs */
	$account = AccountSearch($conn, get_post($conn, 'email')); // Retrieve account from DB using email
	$password = password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
	/* Checks: account should exist with associated email and hashed passwords should match */
	echo "<br>".$account[0]."<br>".$account[1].$account[2]."<br>";
	if(is_null($account)) return false; // User does not exist
	elseif(!password_verify(get_post($conn, 'password'), $account['password'])) return false; // Invalid password
	/* Create and start session */
	session_start();
	ini_set('session.gc_maxlifetime', 60 * 60 * 24); // Setting a Time-Out for cookie
	$_SESSION['account'] = $account; // Store account info in session
	/* Additional Checks: Preventing session hijacking */
	$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
	//echo "Hi {$account['firstname']} {$account['lastname']}, you are now logged in!"; // Replace with JS message
	/* If authenticate user, go to user's homepage based on type */
	if($account['accountType'] == 'admin')
		header("Location: AdminPortal.php"); // ***Change to admin portal
	elseif($account['accountType'] == 'customer')
		header("Location: CustomerPortal.php"); // ***Change to customer portal
	// return true; // User account and password are authenticated
}
