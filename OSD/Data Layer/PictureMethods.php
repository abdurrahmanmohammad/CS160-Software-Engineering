<?php
require_once 'DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/**
 * #######################################################################################
 * ########## Picture: Picture(itemID, directory)                               ##########
 * ########## itemID: CHAR(4) # Store a 4 digit ID                              ##########
 * ########## directory: VARCHAR(200) # Store a 200 character or less directory ##########
 * #######################################################################################
 */

/**
 * @param $conn
 * @return null
 */
function PictureInitialize($conn) {
	if(!$conn) return null; // DB connection must be passed in
	if($conn->connect_error) die(mysql_fatal_error("Could not access DB when building Picture table: ".$conn->error)); // Check connection. If cannot connect to DB, terminate program.
	$query = "CREATE TABLE IF NOT EXISTS Picture ( 
		itemID CHAR(4) NOT NULL, 
		directory VARCHAR(200) NOT NULL,
		PRIMARY KEY (itemID, directory)
	);";
	if(!$conn->query($query)) die(mysql_fatal_error("Could not build table: ".$conn->error)); // Check if statement was executed. If cannot build table, terminate program.
}

function PictureSearch($conn, $itemID) {
	if($stmt = $conn->prepare("SELECT * FROM Picture WHERE itemID=?;")) {
		$stmt->bind_param("s", $itemID);
		$stmt->execute();
		$result = $stmt->get_result();
		$output[] = array(); // 2D array to store query output (array of rows)
		if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
		elseif($rows = $result->num_rows)  // If rows returned: $rows != 0 or $rows != null
			for($i = 0; $i < $rows; $i++) { // Store all entries in table
				$result->data_seek($i); // Get the i^th row
				$output[$i] = $result->fetch_array(MYSQLI_BOTH); // Convert it into an associative array
			}
		// If no rows returned, don't store anything
		$result->close(); // Close statement for security
		return $output; // Return the result as a 2D array
	}
	return null;
}

function PictureInsert($conn, $itemID, $directory) {
	if(!$conn) return false; // DB connection must be passed in
	if(is_null($itemID) || is_null($directory)) return false; // If blank fields
	if(PictureExists($conn, $itemID, $directory) != 0) return false; // If item already exists
	if($stmt = $conn->prepare("INSERT INTO Picture VALUES (?, ?);")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ss', $itemID, $directory); // Bind params for sanitization
		$stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		return true; // Return if successful insert
	}
	return false; // If prepared statement failed, return false
}

function PictureDelete($conn, $itemID, $directory) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("DELETE FROM Picture WHERE itemID=? AND directory=?;")) { // Sanitize vars with prepared statement
		$stmt->bind_param('ss', $itemID, $directory); // Bind params for sanitization
		$output = $stmt->execute(); // Execute statement: Success = TRUE, Failure = FALSE
		$stmt->close(); // Close statement
		if(file_exists($directory))
			unlink($directory); // Check if file exists then delete
		return $output; // Return if successful delete
	}
	return false; // If prepared statement failed, return false
}

function PictureExists($conn, $itemID, $directory) {
	if(!$conn) return null; // DB connection must be passed in
	if($stmt = $conn->prepare("SELECT * FROM Picture WHERE itemID=? AND directory=?;")) {
		$stmt->bind_param("ss", $itemID, $directory);
		$stmt->execute(); // Execute query
		$stmt->store_result(); // Store result
		$rowcount = $stmt->num_rows; // Get number of rows
		$stmt->close(); // Close statement
		return $rowcount; // Return rowcount
	}
}

/**
 * Upload an item picture
 * @param $title
 * @return string|null
 */
function uploadPicture($itemID, $title) {
	if(is_null($title))
		echo <<<_END
		<div class="alert alert-primary" role="alert">Title is null</div>
		_END;
	$target_dir = "Pictures/"; // Target folder to store picture
	$extension = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION)); // Get file extension

	/* Check if file is a valid picture */
	if(!$check = exif_imagetype($_FILES["picture"]["tmp_name"])) {
		echo <<<_END
		<div class="alert alert-primary" role="alert">File is not an image!</div>
		_END;
		return null; // Fail to upload
	}

	/* Create a unique filename */
	$i = 0;
	while(file_exists($target_dir.$title.$itemID."(".$i.").".$extension)) $i++; // Get a unique filename
	$filename = $target_dir.$title.$itemID."(".$i.").".$extension; // Filename: ex. item0.jpg

	/* Check if file is too large */
	/*if($_FILES["picture"]["size"] > 500000) {
		echo "Picture is too large!<br>";
		return false; // Fail to upload
	}*/

	/* Only certain types of pictures are allowed: jpg, jpeg, png, gif */
	if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif") {
		echo <<<_END
		<div class="alert alert-primary" role="alert">Invalid type!<br>Given: $extension<br>Allowed: JPG, JPEG, PNG & GIF</div>
		_END;
		return null; // Fail to upload
	}

	/* Upload file to server */
	if(move_uploaded_file($_FILES["picture"]["tmp_name"], $_SERVER['DOCUMENT_ROOT']."/".$filename))
		echo <<<_END
		<div class="alert alert-primary" role="alert">Picture uploaded: $filename</div>
		_END;
	else {
		echo <<<_END
		<div class="alert alert-primary" role="alert">Could not upload: $filename</div>
		_END;

	}
	return $filename; // Return the filename
}

