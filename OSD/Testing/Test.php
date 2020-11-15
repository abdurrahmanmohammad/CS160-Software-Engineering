<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

ItemInitialize($conn);
ItemInsert($conn, "A101", "Pens", 4.99, 0.23, "This is a very nice Pens");
echo "Item exists (should return true): ".ItemExists($conn, "A101").'<br>';
echo "Item exists (should return false): ".ItemExists($conn, "B101").'<br>';
$item = ItemSearch($conn, "A101", "Pens", 4.99, 0.23, "This is a very nice Pens");
echo $item[0][0].$item[0][1].$item[0][2].$item[0][3].$item[0][4].'<br>';


echo "Test: ItemUpdate<br>";
ItemUpdate($conn, "A101", "Pens", 4.99, 0.23, "This is a very nice Pens",
	"B101", "Pensssssssss", 4444.99, 100.23, "!!!!This is a very nice Pens");

echo "Test: ItemSearch<br>";
$item = ItemSearch($conn, "B101", "Pens", 4.99, 0.23, "This is a very nice Pens");
echo $item[0][0].$item[0][1].$item[0][2].$item[0][3].$item[0][4].'<br>';


echo "Test: ItemSearchByItemID<br>";
$item = ItemSearchByItemID($conn, "A101");
echo $item[0][0].$item[0][1].$item[0][2].$item[0][3].$item[0][4].'<br>';
$item = ItemSearchByItemID($conn, "B101");
echo $item[0][0].$item[0][1].$item[0][2].$item[0][3].$item[0][4].'<br>';

echo "Test: ItemDelete<br>";
//ItemDelete($conn, "A101", "Pens", 4.99, 0.23, "This is a very nice Pens");
//ItemDelete($conn, "B101", "Pensssssssss", 4444.99, 100.23, "!!!!This is a very nice Pens");
