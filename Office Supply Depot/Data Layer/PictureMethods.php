<?php
require_once 'DatabaseMethods.php'; // Load methods for error and sanitization

/**
 * ###################################################################################
 * ########## Pictures(itemID, directory)                                   ##########
 * ########## itemID: CHAR(4)           # Store a 4 digit ID                ##########
 * ########## directory: VARCHAR(200)   # Store a directory (max 200 chars) ##########
 * ###################################################################################
 */

/**
 * Builds the table to store the location of pictures
 * Assertion: conn is a valid DB connection
 * @param $conn
 * @return null
 */
function InitializePictures($conn) {
	/** Establish connection to DB */
	if(!$conn) mysql_fatal_error("Connection cannot be null"); // DB connection must be passed in
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	/** Query string to create table */
	$query = "CREATE TABLE IF NOT EXISTS Pictures ( 
		itemID CHAR(4) NOT NULL, 
		directory VARCHAR(200) NOT NULL,
		PRIMARY KEY (itemID, directory)
	);";
	/** Execute query: If query is not executed, handle error */
	if(!$conn->query($query)) mysql_fatal_error("Could not build Inventory table: ".$conn->error);
	return true; // Successfully built table
}

/**
 * Inserts a picture in the Pictures table
 * Assertion: account with same email does not already exist
 * @param $conn
 * @param $itemID
 * @param $directory
 * @return bool
 */
function InsertPicture($conn, $itemID, $directory) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("ItemID cannot be null");
	if(!$directory) mysql_fatal_error("Directory cannot be null");
	/** Insert: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("INSERT INTO Pictures VALUES (?, ?);")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $directory)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	return true; // Return true after successful insert
}

/**
 * Delete picture form DB and server
 * @param $conn
 * @param $itemID
 * @param $directory
 * @return bool
 */
function DeletePicture($conn, $itemID, $directory) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("ItemID cannot be null");
	if(!$directory) mysql_fatal_error("Directory cannot be null");
	/** Delete: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("DELETE FROM Pictures WHERE itemID=? AND directory=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('ss', $itemID, $directory)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	$stmt->close(); // Close statement
	/** Delete file on server */
	if(file_exists($directory)) unlink($directory); // Check if file exists then delete
	return true; // Return true after successful delete
}


function SearchPicture($conn, $itemID) {
	/** Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("ItemID cannot be null");
	/** Search: sanitize variables with prepared statement */
	if(!$stmt = $conn->prepare("SELECT * FROM Pictures WHERE itemID=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	if(!$stmt->bind_param('s', $itemID)) mysql_fatal_error("Binding parameters failed: ".$conn->error); // Bind parameters for sanitization
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error); // Execute statement
	/** Get result */
	$result = $stmt->get_result(); // Get query result
	if($result->num_rows == 0) return null; // If result is empty, return null
	/** Store each row of result in output */
	$output[] = array(); // 2D array to store query output (array of rows)
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
	elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
		for($i = 0; $i < $rows; $i++) { // Store all entries in table
			$result->data_seek($i); // Get the i^th row
			$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
		}
	$stmt->close(); // Close statement
	return $output; // Return output
}


/**
 * Upload an item picture
 * @param $itemID
 * @param $title
 * @return string|null
 */
function UploadPicture($itemID, $title) {
	/** Validate inputs */
	if(!$itemID) {
		echo '<div class="alert alert-primary" role="alert">Item ID is null</div>';
		return null;
	}
	if(!$title) {
		echo '<div class="alert alert-primary" role="alert">Title is null</div>';
		return null;
	}
	/** Setup upload vars */
	$target_dir = "../Pictures/"; // Target folder to store picture
	$extension = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION)); // Get file extension
	/** Check if file is a valid picture */
	if(!$check = exif_imagetype($_FILES["picture"]["tmp_name"])) {
		echo '<div class="alert alert-primary" role="alert">File is not an image!</div>';
		return null; // Fail to upload
	}
	/** Create a unique filename */
	$i = 0;
	while(file_exists($target_dir.$title.$itemID."(".$i.").".$extension)) $i++; // Get a unique filename
	$filename = $target_dir.$title.$itemID."(".$i.").".$extension; // Filename: ex. item0.jpg
	/* Check if file is too large */
	/*if($_FILES["picture"]["size"] > 500000) {
		echo "Picture is too large!<br>";
		return null; // Fail to upload
	}*/
	/** Only certain types of pictures are allowed: jpg, jpeg, png, gif */
	if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif") {
		echo '<div class="alert alert-primary" role="alert">Invalid type!<br>Given: '.$extension.'<br>Allowed: JPG, JPEG, PNG & GIF</div>';
		return null; // Fail to upload
	}
	/** Upload file to server */
	if(!move_uploaded_file($_FILES["picture"]["tmp_name"], $filename)) {
		echo '<div class="alert alert-primary" role="alert">Could not upload: '.$filename.'</div>';
		return null;
	}
	/** Success */
	echo '<div class="alert alert-primary" role="alert">Picture uploaded: '.$filename.'</div>'; // Success
	return $filename; // Return the filename
}

/**
 * ################################################################################################
 * ######################################## Unused Methods ########################################
 * ################################################################################################
 */

/**
 * Check if a certain picture exists in DB
 * Update: Use the search method instead
 * @param $conn
 * @param $itemID
 * @param $directory
 * @return mixed
 */
function PictureExists($conn, $itemID, $directory) {
	/* Check if parameters are valid/null */
	if(!$conn) mysql_fatal_error("Connection cannot be null");
	if($conn->connect_error) mysql_fatal_error($conn->connect_error); // Test connection
	if(!$itemID) mysql_fatal_error("ItemID cannot be null");
	if(!$directory) mysql_fatal_error("Directory cannot be null");
	/* Perform query */
	if(!$stmt = $conn->prepare("SELECT * FROM Pictures WHERE itemID=? AND directory=?;")) mysql_fatal_error("Prepare statement failed: ".$conn->error);
	/* Bind parameters for sanitization */
	if(!$stmt->bind_param("ss", $itemID, $directory)) mysql_fatal_error("Binding parameters failed: ".$conn->error);
	/* Execute statement */
	if(!$stmt->execute()) mysql_fatal_error("Execute failed: ".$conn->error);
	/* Store result */
	if(!$stmt->store_result()) mysql_fatal_error("Store result failed : ".$conn->error);
	/* Get rowcount */
	$rowcount = $stmt->num_rows;
	/* Close statement */
	$stmt->close();
	return $rowcount; // Return true after successful insert
}
