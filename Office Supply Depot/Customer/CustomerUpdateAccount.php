<?php
require_once '../Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once '../Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate('customer'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Retrieve email */
$email = $account['email'];

/** Retrieve account associated with email */
$account = SearchAccountByEmail($conn, $email); // Retrieve account

echo <<<_END
<html>
<head>
    <title>Update User</title>
      <link rel="stylesheet" href="../CSS/bootstrap.min.css">
      <script src="../JS/jquery-3.2.1.slim.min.js"></script>
      <script src="../JS/popper.min.js"></script>
      <script src="../JS/bootstrap.min.js"></script>
      <script src="../JS/jquery-1.10.2.js"></script>
      <!-- Start: Google Maps API -->
      <link rel="stylesheet" href="../CSS/GoogleMapsAPI.css">
      <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=?&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
      <script src="../JS/GoogleMapsAPI.js"></script>
      <!-- End: Google Maps API -->
</head>
<body>
   <div id="nav-placeholder"></div>
   <script>
      $.get("./nav_customer.html", function (data) {
          $("#nav-placeholder").replaceWith(data);
      });
   </script>
<div class="container">
    <h1>Update User</h1>
    <hr>
_END;

// Check if the fields are set
if(isset($_POST['update'])) {
	// Check if all the necessary fields are set
	if(isset($_POST['email'], $_POST['password'], $_POST['account_type'], $_POST['firstname'], $_POST['lastname'], $_POST['phone'])) {
		// Check if we need to update address: If address is same as old address, keep it. If not, validate address and update
		$address = null;
		if(get_post($conn, 'address') == $account['address']) {
			// If address was not updated: keep it
			$address = get_post($conn, 'address');

			// ########## Start: Update account ##########
			// Check if password was updated. If so, salt and hash the new password before storing.
			$password = $account['password'] == get_post($conn, 'password') ? $account['password'] : password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
			UpdateAccount($conn, get_post($conn, 'email'), $password, get_post($conn, 'account_type'), get_post($conn, 'firstname'), get_post($conn, 'lastname'), get_post($conn, 'phone'), $address);
			echo '<div class="alert alert-primary" role="alert">Account updated!</div>';
			$account = SearchAccountByEmail($conn, $email); // Retrieve updated account
			// ########## End: Update account ##########
		} else if($_POST['street_number'] != '' && $_POST['route'] != '' && $_POST['locality'] != '' && $_POST['administrative_area_level_1'] != '' && $_POST['postal_code'] != '' && $_POST['country'] != '') {
			// If address was updated: Validate address fields and get new address
			$address = $_POST['street_number']." ".$_POST['route']." ".$_POST['locality']." ".$_POST['administrative_area_level_1']." ".$_POST['postal_code']." ".$_POST['country'];

			// ########## Start: Update account ##########
			// Check if password was updated. If so, salt and hash the new password before storing.
			$password = $account['password'] == get_post($conn, 'password') ? $account['password'] : password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
			UpdateAccount($conn, get_post($conn, 'email'), $password, get_post($conn, 'account_type'), get_post($conn, 'firstname'), get_post($conn, 'lastname'), get_post($conn, 'phone'), $address);
			echo '<div class="alert alert-primary" role="alert">Account updated!</div>';
			$account = SearchAccountByEmail($conn, $email); // Retrieve updated account
			// ########## End: Update account ##########
		} else echo '<div class="alert alert-primary" role="alert">Address must be valid!</div>';
	} else '<div class="alert alert-primary" role="alert">Cannot have empty fields!</div>';
}


echo <<<_END
<form action="CustomerUpdateAccount.php" method="POST" enctype="multipart/form-data">
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
        <label for="account_type">Account Type</label>
        <input type="text" class="form-control" name="account_type" placeholder="Account Type"
               value="{$account['account_type']}" required readonly>
    </div>
    <div class="form-group">
        <label for="firstname">First name</label>
        <input type="text" pattern="[A-Za-z]+" class="form-control" name="firstname" placeholder="Enter first name"
               value="{$account['firstname']}" required>
    </div>
    <div class="form-group">
        <label for="lastname">Last name</label>
        <input type="text" pattern="[A-Za-z]+" class="form-control" name="lastname" placeholder="Enter last name"
               value="{$account['lastname']}" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter 10 digit phone number: 0123456789" 
        pattern="\d*" minlength="10" maxlength="10" value="{$account['phone']}" required>
    </div>
	
	<p>Address</p>
	<div class="shippingAddr" id="locationField">
      <div class="form-row">
         <input name="address" class="form-control" id="autocomplete" value="{$account['address']}" onFocus="geolocate()" type="text">
         <br><br>
      </div>
      <div class="form-row">
         <div class="form-group col-md-6">
            <label for="street_number">Street Number</label>
            <input class="field" id="street_number" name="street_number" disabled="true" readonly required>
         </div>
         <div class="form-group col-md-6">
            <label for="route">Street Name</label>
            <input class="field" id="route" name="route" disabled="true" readonly required>
         </div>
      </div>
      <div class="form-row">
         <div class="form-group col-md-6">
            <label for="locality">City</label>
            <input class="field" id="locality" name="locality" disabled="true" readonly required>
         </div>
         <div class="form-group col-md-4">
            <label for="administrative_area_level_1">State</label>
            <input class="field" id="administrative_area_level_1" name="administrative_area_level_1" disabled="true" readonly required>
         </div>
         <div class="form-group col-md-2">
            <label for="postal_code">Zip</label>
            <input class="field" id="postal_code" name="postal_code" disabled="true" readonly required>
         </div>
         <div class="form-group col-md-4">
            <label for="country">Country</label>
            <input class="field" id="country" name="country" disabled="true" readonly required>
         </div>
      </div>
	
    <div class="form-group">
        <button type="submit" class="btn btn-primary" name="update">Update</button>
        <a href="CustomerPortal.php" class="btn">
            <button type="button" class="btn btn-primary">Back</button>
        </a>
    </div>
</form>
</div>
</body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting