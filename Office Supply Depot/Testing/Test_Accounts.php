<?php
/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/AccountMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php';

/** Set up connection to DB */
$conn = getConnection();

echo "#################### Accounts Test ####################: <br>";

/** Initialize DB */
echo "Test Initialize: ".InitializeAccounts($conn)."<br>";

/** Test Delete */
echo "<br>Test Delete: <br>";
echo "Account 1 deleted: ".DeleteAccount($conn, "1")."<br>";
echo "Account 2 deleted: ".DeleteAccount($conn, "2")."<br>";
echo "Account 3 deleted: ".DeleteAccount($conn, "3")."<br>";

/** Test Insert */
echo "<br>Test Insert: <br>";
echo "Account 1 inserted: ".InsertAccount($conn, "1", "xxx", "xxx", "xxx", "xxx", "xxx", "xxx")."<br>";
echo "Account 2 inserted: ".InsertAccount($conn, "2", "xxx", "xxx", "xxx", "xxx", "xxx", "xxx")."<br>";
echo "Account 3 inserted: ".InsertAccount($conn, "3", "xxx", "xxx", "xxx", "xxx", "xxx", "xxx")."<br>";

/** Test Exists */
echo "<br>Test Exists: <br>";
echo "Account 1 exists: ".AccountExists($conn, "1")."<br>";
echo "Account 2 exists: ".AccountExists($conn, "2")."<br>";
echo "Account 3 exists: ".AccountExists($conn, "3")."<br>";

/** Test Search by email*/
echo "<br>Test Search By Email: <br>";
$search = SearchAccountByEmail($conn, "1");
echo "Account 1: ".$search[0]."<br>";
$search = SearchAccountByEmail($conn, "2");
echo "Account 2: ".$search[0]."<br>";
$search = SearchAccountByEmail($conn, "3");
echo "Account 3: ".$search[0]."<br>";

echo "<br>Test Search By Account Type: <br>";
$search = SearchAccountByAccountType($conn, "xxx");
echo "Account 1: ".$search[0]['email']."<br>";
echo "Account 2: ".$search[1]['email']."<br>";
echo "Account 3: ".$search[2]['email']."<br>";

/** Test Update */
echo "<br>Test Update: <br>";
echo "Update Account 1 (should be 1): ".UpdateAccount($conn, "1", "aaa", "aaa", "aaa", "aaa", "aaa", "aaa")."<br>";
echo "Update Account 1 (should be 1): ".UpdateAccount($conn, "2", "aaa", "aaa", "aaa", "aaa", "aaa", "aaa")."<br>";
echo "Update Account 1 (should be 1): ".UpdateAccount($conn, "3", "aaa", "aaa", "aaa", "aaa", "aaa", "aaa")."<br>";

/** Test Delete */
echo "<br>Test Delete: <br>";
echo "Account 1 deleted: ".DeleteAccount($conn, "1")."<br>";
echo "Account 2 deleted: ".DeleteAccount($conn, "2")."<br>";
echo "Account 3 deleted: ".DeleteAccount($conn, "3")."<br>";