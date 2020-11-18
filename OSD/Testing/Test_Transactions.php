<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/OrderMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/TransactionsMethods.php';

/** Set up connection to DB */
$conn = getConnection();

/** Generate order ID's */
echo "Generating orderID's: <br>";
$orderID_1 = GenerateOrderID($conn); // Generate an orderID
$orderID_2 = GenerateOrderID($conn); // Generate an orderID
echo "OrderID 1: ".$orderID_1."<br>";
echo "OrderID 2: ".$orderID_2."<br>";

echo "<br>Test Insert:<br>";
$email = "testing@test.com";
echo "Transaction1 [$orderID_1] insert: ".TransactionsInsert($conn, $orderID_1, 121.23, "Jeff Bezos", "1111111111111111", "12", "22", "123")."<br>";
echo "Transaction2 [$orderID_2] insert: ".TransactionsInsert($conn, $orderID_2, 23.25, "Bob", "2222222222222222", "01", "20", "323")."<br>";

echo "<br>Test Exists:<br>";
echo "Transaction1 [$orderID_1] exists: ".TransactionsExists($conn, $orderID_1)."<br>";
echo "Transaction2 [$orderID_2] exists: ".TransactionsExists($conn, $orderID_2)."<br>";

echo "<br>Test search by orderID:<br>";
$search_1 = TransactionsSearch($conn, $orderID_1);
$search_2 = TransactionsSearch($conn, $orderID_1);
echo "Transaction1 [$orderID_1] search: ".$search_1['orderID']."<br>";
echo "Transaction2 [$orderID_2] search: ".$search_2['orderID']."<br>";

echo "<br>Test Delete:<br>";
echo "Transaction1 [$orderID_1] delete: ".TransactionsDelete($conn, $orderID_1)."<br>";
echo "Transaction2 [$orderID_2] delete: ".TransactionsDelete($conn, $orderID_2)."<br>";
echo "Done!";
