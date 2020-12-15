<?php
/** Import methods */
$root = $_SERVER['DOCUMENT_ROOT']; // Root of project
require_once "$root/Data Layer/DatabaseMethods.php";
require_once "$root/Data Layer/AccountMethods.php";

/** Set up connection to DB */
$conn = getConnection();

/** Print login page */
echo <<< _END
<html>
   <head>
      <title>Sign In</title>
      <link rel="stylesheet" href="CSS/bootstrap.min.css">
      <link rel="stylesheet" href="CSS/main.css">
      <script src="JS/jquery-3.2.1.slim.min.js"></script>
      <script src="JS/popper.min.js"></script>
      <script src="JS/bootstrap.min.js"></script>
      <script src="JS/jquery-1.10.2.js"></script>
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
if($_REQUEST['logout']) {
	destroy_session_and_data(); // Destroy session and go to sign in page
	echo '<div class="alert alert-primary" role="alert">You have been successfully logged out!</div>';
}

/** Sign in */
if(isset($_POST['email'], $_POST['password'])) {
	$account = CheckPassword($conn); // Get user account from DB
	if(!$account) echo '<div class="alert alert-primary" role="alert">Invalid username/password combination!</div>'; // If account is null
	else { // Start session and authenticate user
		session_start();
		/* Setting a Time-Out for cookie */
		ini_set('session.gc_maxlifetime', 60 * 60 * 24);
		/* Store user's account in session */
		$_SESSION['account'] = $account;
		/* Additional Checks: Preventing session hijacking */
		$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
		/* If authenticate user, go to user's homepage based on type */
		/* Redirect user to their respective portal */
		if($account['account_type'] == 'admin') header("Location: ./Admin/AdminPortal.php"); // Change to admin portal
		elseif($account['account_type'] == 'customer') header("Location: ./Customer/CustomerPortal.php"); // Change to customer portal
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

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting