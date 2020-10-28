<?php

function getConnection(){
    require 'config.php';
    $conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
    if($conn->connect_error) // Check connection. If cannot connect to DB, terminate program.
        die(mysql_fatal_error("Could not access DB: ".$conn->error));
    return $conn;
}

function listAllCategory() {
    $conn = getConnection();
	$sql = "SELECT * FROM category";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $output = array(); // Stores query output
	while($row = $result->fetch_assoc()) {
        $output[] = $row;
    }
    $conn->close();
	return $output; // Return the result as a 2D array
}

function getCategory($categoryId) {
    $conn = getConnection();
	$sql = "SELECT * FROM category WHERE `category`.`id` = $categoryId";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $output = array(); // Stores query output
	while($row = $result->fetch_assoc()) {
        $output = $row;
    }
    $conn->close();
	return $output; // Return the result as a 2D array
}

function insertCategory($categoryName) {
    $conn = getConnection();
	$sql = "INSERT INTO `category` (`id`, `name`) VALUES (NULL, '$categoryName');";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}

function updateCategory($categoryId, $categoryName) {
    $conn = getConnection();
	$sql = "UPDATE `category` SET `name` = '$categoryName' WHERE `category`.`id` = $categoryId;";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}

function deleteCategory($categoryId) {
    $conn = getConnection();
	$sql = "DELETE FROM `category` WHERE `category`.`id` = $categoryId";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}