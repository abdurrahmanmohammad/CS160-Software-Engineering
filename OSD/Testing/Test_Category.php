<?php
/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/CategoryMethods.php';

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection


echo "Test Insert: <br>";
echo "Category 1234 inserted: ".CategoryInsert($conn, "1234", "cat. 1234")."<br>";
echo "Category 0000 inserted: ".CategoryInsert($conn, "0000", "cat. 0000")."<br>";
echo "Category 1111 inserted: ".CategoryInsert($conn, "1111", "cat. 1111")."<br>";
echo "Category xxxx inserted: ".CategoryInsert($conn, "xxxx", "cat. xxxx1")."<br>"; // xxxx
echo "Category xxxx inserted: ".CategoryInsert($conn, "xxxx", "cat. xxxx2")."<br>"; // xxxx
echo "Category xxxx inserted: ".CategoryInsert($conn, "xxxx", "cat. xxxx3")."<br>"; // xxxx

echo "<br>Test Exists: <br>";
echo "Category 1234 exists: ".CategoryExists($conn, "1234", "cat. 1234")."<br>";
echo "Category 0000 exists: ".CategoryExists($conn, "0000", "cat. 0000")."<br>";
echo "Category 1111 exists: ".CategoryExists($conn, "1111", "cat. 1111")."<br>";
echo "Category xxxx exists: ".CategoryExists($conn, "xxxx", "cat. xxxx1")."<br>"; // xxxx
echo "Category xxxx exists: ".CategoryExists($conn, "xxxx", "cat. xxxx2")."<br>"; // xxxx
echo "Category xxxx exists: ".CategoryExists($conn, "xxxx", "cat. xxxx3")."<br>"; // xxxx

echo "<br>Test Search: <br>";
$search = CategorySearch($conn, "xxxx");
echo "Category xxxx 1: ".$search[0][1]."<br>"; // xxxx
echo "Category xxxx 2: ".$search[1][1]."<br>"; // xxxx
echo "Category xxxx 3: ".$search[2][1]."<br>"; // xxxx

echo "<br>Test Delete: <br>";
echo "Category 1234 deleted: ".CategoryDelete($conn, "1234", "cat. 1234")."<br>";
echo "Category 0000 deleted: ".CategoryDelete($conn, "0000", "cat. 0000")."<br>";
echo "Category 1111 deleted: ".CategoryDelete($conn, "1111", "cat. 1111")."<br>";
echo "Category xxxx deleted: ".CategoryDelete($conn, "xxxx", "cat. xxxx1")."<br>"; // xxxx
echo "Category xxxx deleted: ".CategoryDelete($conn, "xxxx", "cat. xxxx2")."<br>"; // xxxx
echo "Category xxxx deleted: ".CategoryDelete($conn, "xxxx", "cat. xxxx3")."<br>"; // xxxx
echo "Done!";