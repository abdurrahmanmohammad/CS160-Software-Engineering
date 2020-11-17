<?php


require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Directory of file
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization


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
	<div id="nav-placeholder"></div>
	<script>
    	$.get("./nav.html", function (data) {
        	$("#nav-placeholder").replaceWith(data);
    	});
	</script>
_END;

/** If logout */
if($_GET['logout']) {
	destroy_session_and_data(); // Destroy session and go to sign in page
	echo <<<_END
		<div class="alert alert-primary" role="alert">You have been successfully logged out!</div>
	_END; // Print error message
}


/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

if(isset($_POST['email']) && isset($_POST['password'])) {
	$account = CheckPassword($conn); // Get account of user from DB
	if(is_null($account))
		echo <<<_END
		<div class="alert alert-primary" role="alert">Invalid username/password combination!</div>
		_END; // Print error message
	else { // Start session and authenticate user
		session_start();
		ini_set('session.gc_maxlifetime', 60 * 60 * 24); // Setting a Time-Out for cookie
		$_SESSION['account'] = $account; // Store user's account in session
		/* Additional Checks: Preventing session hijacking */
		$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
		/* Redirect user to their portal */
		if($account['accountType'] == 'admin') header("Location: AdminPortal.php"); // Change to admin portal
		elseif($account['accountType'] == 'customer') header("Location: CustomerPortal.php"); // Change to customer portal
	}
}



echo <<< _END
	<form class="form-signin" action="Signin.php" method="post">
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
 */
function CheckPassword($conn) {
	/* Retrieve inputs */
	$account = AccountSearch($conn, get_post($conn, 'email')); // Retrieve account from DB using email
	$password = password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
	/* Checks: account should exist with associated email and hashed passwords should match */
	if(is_null($account)) return null; // User does not exist
	elseif(!password_verify(get_post($conn, 'password'), $account['password'])) return null; // Invalid password
	/* Create and start session */
	session_start();
	ini_set('session.gc_maxlifetime', 60 * 60 * 24); // Setting a Time-Out for cookie
	$_SESSION['account'] = $account; // Store account info in session
	/* Additional Checks: Preventing session hijacking */
	$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
	//echo "Hi {$account['firstname']} {$account['lastname']}, you are now logged in!"; // Replace with JS message
	/* If authenticate user, go to user's homepage based on type */
	return $account; // User account and password are authenticated
}

/*echo <<< _END
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
_END;*/