<?php
require_once '../Data Layer/AccountMethods.php'; // Load methods for error and sanitization
require_once '../Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();

/** Retrieve email */
$email = get_post($conn, 'email');

/** If email is null */
if(!$email) header("Location: AdminPortal.php"); // If no itemID passed in, go to item management

/** Retrieve account associated with email */
$user_account = SearchAccountByEmail($conn, $email); // Retrieve account

/** Determine previous page based on account type */
$prevPage = ($account['account_type'] == "admin") ? "ManageAdmins.php" : "ManageCustomers.php";

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
      <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=?&callback=initAutocomplete&libraries=places&v=weekly"
      defer
    ></script>
    <style type="text/css">
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }

      /* Optional: Makes the sample page fill the window. */
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      #locationField,
      #controls {
        position: relative;
        width: 480px;
      }

      #autocomplete {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 99%;
      }

      .label {
        text-align: right;
        font-weight: bold;
        width: 100px;
        color: #303030;
        font-family: "Roboto", Arial, Helvetica, sans-serif;
      }

      #address {
        border: 1px solid #000090;
        background-color: #f0f9ff;
        width: 480px;
        padding-right: 2px;
      }

      #address td {
        font-size: 10pt;
      }

      .field {
        width: 99%;
      }

      .slimField {
        width: 80px;
      }

      .wideField {
        width: 200px;
      }

      #locationField {
        height: 20px;
        margin-bottom: 2px;
      }
    </style>
    <script>
      // This sample uses the Autocomplete widget to help the user select a
      // place, then it retrieves the address components associated with that
      // place, and then it populates the form fields with those details.
      // This sample requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script
      // src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      let placeSearch;
      let autocomplete;
      const componentForm = {
        street_number: "short_name",
        route: "long_name",
        locality: "long_name",
        administrative_area_level_1: "short_name",
        country: "long_name",
        postal_code: "short_name",
      };

      function initAutocomplete() {
        // Create the autocomplete object, restricting the search predictions to
        // geographical location types.
        autocomplete = new google.maps.places.Autocomplete(
          document.getElementById("autocomplete"),
          { types: ["geocode"] }
        );
        // Avoid paying for data that you don't need by restricting the set of
        // place fields that are returned to just the address components.
        autocomplete.setFields(["address_component"]);
        // When the user selects an address from the drop-down, populate the
        // address fields in the form.
        autocomplete.addListener("place_changed", fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        const place = autocomplete.getPlace();

        for (const component in componentForm) {
          document.getElementById(component).value = "";
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details,
        // and then fill-in the corresponding field on the form.
        for (const component of place.address_components) {
          const addressType = component.types[0];

          if (componentForm[addressType]) {
            const val = component[componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        }
      }

      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition((position) => {
            const geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude,
            };
            const circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy,
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }
    </script>
      <!-- End: Google Maps API -->
</head>
<body>
<div id="nav-placeholder"></div>
<script>
    $.get("./admin_navbar.html", function(data){
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
		if(get_post($conn, 'address') == $user_account['address']) {
			// If address was not updated: keep it
			$address = get_post($conn, 'address');

			// ########## Start: Update account ##########
			// Check if password was updated. If so, salt and hash the new password before storing.
			$password = $user_account['password'] == get_post($conn, 'password') ? $user_account['password'] : password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
			UpdateAccount($conn, get_post($conn, 'email'), $password, get_post($conn, 'account_type'), get_post($conn, 'firstname'), get_post($conn, 'lastname'), get_post($conn, 'phone'), $address);
			echo '<div class="alert alert-primary" role="alert">Account updated!</div>';
			$user_account = SearchAccountByEmail($conn, $email); // Retrieve updated account
			// ########## End: Update account ##########
		} else if($_POST['street_number'] != '' && $_POST['route'] != '' && $_POST['locality'] != '' && $_POST['administrative_area_level_1'] != '' && $_POST['postal_code'] != '' && $_POST['country'] != '') {
			// If address was updated: Validate address fields and get new address
			$address = $_POST['street_number']." ".$_POST['route']." ".$_POST['locality']." ".$_POST['administrative_area_level_1']." ".$_POST['postal_code']." ".$_POST['country'];

			// ########## Start: Update account ##########
			// Check if password was updated. If so, salt and hash the new password before storing.
			$password = $user_account['password'] == get_post($conn, 'password') ? $user_account['password'] : password_hash(get_post($conn, 'password'), PASSWORD_BCRYPT); // Salt the password with a random salt and hash (60 chars)
			UpdateAccount($conn, get_post($conn, 'email'), $password, get_post($conn, 'account_type'), get_post($conn, 'firstname'), get_post($conn, 'lastname'), get_post($conn, 'phone'), $address);
			echo '<div class="alert alert-primary" role="alert">Account updated!</div>';
			$user_account = SearchAccountByEmail($conn, $email); // Retrieve updated account
			// ########## End: Update account ##########
		} else echo '<div class="alert alert-primary" role="alert">Address must be valid!</div>';
	} else '<div class="alert alert-primary" role="alert">Cannot have empty fields!</div>';
}


echo <<<_END
<form method="POST">
    <div class="form-group">
        <label for="email">Username</label>
        <input type="text" class="form-control" name="email" placeholder="Enter user name"
               value="{$user_account['email']}" required readonly>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" minlength="8" 
        	maxlength="64" placeholder="Enter password" value="{$user_account['password']}" required>
    </div>
    <div class="form-group">
        <label for="account_type">Account Type</label>
        <input type="text" class="form-control" name="account_type" placeholder="Account Type"
               value="{$user_account['account_type']}" required readonly>
    </div>
    <div class="form-group">
        <label for="firstname">First name</label>
        <input type="text" pattern="[A-Za-z]+" class="form-control" name="firstname" placeholder="Enter first name"
               value="{$user_account['firstname']}" required>
    </div>
    <div class="form-group">
        <label for="lastname">Last name</label>
        <input type="text" pattern="[A-Za-z]+" class="form-control" name="lastname" placeholder="Enter last name"
               value="{$user_account['lastname']}" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter 10 digit phone number: 0123456789" 
        pattern="\d*" minlength="10" maxlength="10" value="{$user_account['phone']}" required>
    </div>
	
	<p>Address</p>
	<div class="shippingAddr" id="locationField">
      <div class="form-row">
         <input name="address" class="form-control" id="autocomplete" value="{$user_account['address']}" onFocus="geolocate()" type="text">
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
        <button name="update" type="submit" class="btn btn-primary">Update</button>
        <a href="$prevPage" class="btn">
            <button type="button" class="btn btn-primary">Back to user list</button>
        </a>
    </div>
</form>
</div>
</body>
</html>
_END;

/** Close DB connection before exiting */
$conn->close(); // Close the connection before exiting