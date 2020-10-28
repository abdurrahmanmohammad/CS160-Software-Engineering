<?php

function getConnection(){
    require 'config.php';
    $conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
    if($conn->connect_error) // Check connection. If cannot connect to DB, terminate program.
        die(mysql_fatal_error("Could not access DB: ".$conn->error));
    return $conn;
}

function listAllUser() {
    $conn = getConnection();
	$sql = "SELECT * FROM user";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $output = array(); // Stores query output
	while($row = $result->fetch_assoc()) {
        $output[] = $row;
    }
    $conn->close();
	return $output; // Return the result as a 2D array
}

function getUser($userId) {
    $conn = getConnection();
	$sql = "SELECT * FROM user WHERE `user`.`id` = $userId";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $output = array(); // Stores query output
	while($row = $result->fetch_assoc()) {
        $output = $row;
    }
    $conn->close();
	return $output;
}

function checkUsernameExist($username) {
    $conn = getConnection();
	$sql = "SELECT * FROM user WHERE `user`.`username` = '$username'";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $output = false; // Stores query output
	while($row = $result->fetch_assoc()) {
        $output = true;
    }
    $conn->close();
	return $output;
}

function insertUser($userData) {
    $conn = getConnection();
    $username = $userData["username"];
    $password = md5($userData["password"]);
    $role = $userData["role"];
    $fullName = $userData["fullName"];
    $address = $userData["address"];
	$sql = "INSERT INTO `user` (`id`, `username`, `password`, `role`, `fullName`, `address`) VALUES (NULL, '$username', '$password', '$role', '$fullName', '$address');";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}

function updateUserInfo($userId, $userData) {
    $conn = getConnection();
    $role = $userData["role"];
    $fullName = $userData["fullName"];
    $address = $userData["address"];
	$sql = "UPDATE `user` SET `role` = '$role', `fullName` = '$fullName', `address` = '$address' WHERE `user`.`id` = $userId;";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}

function updateUserPassword($userId, $password) {
    $conn = getConnection();
    $password = md5($password);
	$sql = "UPDATE `user` SET `password` = '$password'";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}

function deleteUser($userId) {
    $conn = getConnection();
	$sql = "DELETE FROM `user` WHERE `user`.`id` = $userId";
    $result = $conn->query($sql);
	if(!$result) die(mysql_fatal_error($conn->error)); // Error: execute custom error function
    $conn->close();
	return true;
}