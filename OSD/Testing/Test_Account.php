<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/Login.php'; // Import database credentials
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php'; // Load methods for error and sanitization

/** Set up connection to DB */
$conn = new mysqli($hn, $un, $pw, $db); // Create a connection to the database
if($conn->connect_error) die(mysql_fatal_error($conn->connect_error)); // Test connection

echo "#################### Account Test ####################: <br>";

/** Initialize DB */
echo "Test Initialize: ".AccountInitialize($conn)."<br>";

/** Test Insert */
echo "Test Insert: <br>";
echo "Account 1 inserted: ".AccountInsert($conn, "1", "xxx", "xxx", "xxx", "xxx", "xxx", "xxx")."<br>";
echo "Account 2 inserted: ".AccountInsert($conn, "2", "xxx", "xxx", "xxx", "xxx", "xxx", "xxx")."<br>";
echo "Account 3 inserted: ".AccountInsert($conn, "3", "xxx", "xxx", "xxx", "xxx", "xxx", "xxx")."<br>";

/** Test Exists */
echo "<br>Test Exists: <br>";
echo "Account 1 exists: ".AccountExists($conn, "1")."<br>";
echo "Account 2 exists: ".AccountExists($conn, "1")."<br>";
echo "Account 3 exists: ".AccountExists($conn, "1")."<br>";

/** Test Search */
echo "<br>Test Search: <br>";
$search = AccountSearch($conn, "1");
echo "Account 1: ".$search[0]."<br>";
$search = AccountSearch($conn, "2");
echo "Account 1: ".$search[0]."<br>";
$search = AccountSearch($conn, "3");
echo "Account 1: ".$search[0]."<br>";

/** Test Update */
echo "<br>Test Update: <br>";
echo "Update Account 1 (should be null): ".AccountUpdate($conn, "0", "4", "aaa", "aaa", "aaa", "aaa", "aaa", "aaa")."<br>";
echo "Update Account 1 (should be null): ".AccountUpdate($conn, "1", "2", "aaa", "aaa", "aaa", "aaa", "aaa", "aaa")."<br>";
echo "Update Account 1 (should be 1): ".AccountUpdate($conn, "1", "4", "aaa", "aaa", "aaa", "aaa", "aaa", "aaa")."<br>";

/** Test Delete */
echo "<br>Test Delete: <br>";
echo "Account 1 deleted: ".AccountDelete($conn, "1")."<br>";
echo "Account 2 deleted: ".AccountDelete($conn, "2")."<br>";
echo "Account 3 deleted: ".AccountDelete($conn, "3")."<br>";