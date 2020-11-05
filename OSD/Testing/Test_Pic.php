<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/ItemMethods.php'; // Load item database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/InventoryMethods.php'; // Load inventory database methods
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PictureMethods.php'; // Load inventory database methods

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

echo "Test Insert: <br>";
echo "Picture 1234 inserted: ".PictureInsert($conn, "1234", "/test/test1234")."<br>";
echo "Picture 0000 inserted: ".PictureInsert($conn, "0000", "/test/test0000")."<br>";
echo "Picture 1111 inserted: ".PictureInsert($conn, "1111", "/test/test1111")."<br>";
echo "Picture xxxx inserted: ".PictureInsert($conn, "xxxx", "/test/xxx1")."<br>"; // xxxx
echo "Picture xxxx inserted: ".PictureInsert($conn, "xxxx", "/test/xxx2")."<br>"; // xxxx
echo "Picture xxxx inserted: ".PictureInsert($conn, "xxxx", "/test/xxx3")."<br>"; // xxxx

echo "<br>Test Exists: <br>";
echo "Picture 1234 exists: ".PictureExists($conn, "1234", "/test/test1234")."<br>";
echo "Picture 0000 exists: ".PictureExists($conn, "0000", "/test/test0000")."<br>";
echo "Picture 1111 exists: ".PictureExists($conn, "1111", "/test/test1111")."<br>";
echo "Picture xxxx exists: ".PictureExists($conn, "xxxx", "/test/xxx1")."<br>"; // xxxx
echo "Picture xxxx exists: ".PictureExists($conn, "xxxx", "/test/xxx2")."<br>"; // xxxx
echo "Picture xxxx exists: ".PictureExists($conn, "xxxx", "/test/xxx3")."<br>"; // xxxx

echo "<br>Test Search: <br>";
$search = PictureSearch($conn, "xxxx");
echo "Picture xxxx 1: ".$search[0][1]."<br>"; // xxxx
echo "Picture xxxx 2: ".$search[1][1]."<br>"; // xxxx
echo "Picture xxxx 3: ".$search[2][1]."<br>"; // xxxx

echo "<br>Test Delete: <br>";
echo "Picture 1234 deleted: ".PictureDelete($conn, "1234", "/test/test1234")."<br>";
echo "Picture 0000 deleted: ".PictureDelete($conn, "0000", "/test/test0000")."<br>";
echo "Picture 1111 deleted: ".PictureDelete($conn, "1111", "/test/test1111")."<br>";
echo "Picture xxxx deleted: ".PictureDelete($conn, "xxxx", "/test/xxx1")."<br>"; // xxxx
echo "Picture xxxx deleted: ".PictureDelete($conn, "xxxx", "/test/xxx2")."<br>"; // xxxx
echo "Picture xxxx deleted: ".PictureDelete($conn, "xxxx", "/test/xxx3")."<br>"; // xxxx
echo "Done!";