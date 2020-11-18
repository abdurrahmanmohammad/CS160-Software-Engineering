<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/OrderMethods.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/PurchasesMethods.php';

/** Set up connection to DB */
$conn = getConnection();

/** Generate order ID's */
echo "Generating orderID's: <br>";
$orderID_1 = GenerateOrderID($conn); // Generate an orderID
$orderID_2 = GenerateOrderID($conn); // Generate an orderID
echo "OrderID 1: ".$orderID_1."<br>";
echo "OrderID 2: ".$orderID_2."<br>";

echo "<br>Test Insert:<br>";
echo "Purchase1 [$orderID_1] insert: ".PurchasesInsert($conn, $orderID_1, "xxxx", 15.23, 2)."<br>";
echo "Purchase2 [$orderID_2] insert: ".PurchasesInsert($conn, $orderID_2, "xxxx", 23.12, 5)."<br>";

echo "<br>Test Exists:<br>";
echo "Purchase1 [$orderID_1] exists: ".PurchasesExists($conn, $orderID_1)."<br>";
echo "Purchase2 [$orderID_2] exists: ".PurchasesExists($conn, $orderID_2)."<br>";

echo "<br>Test search by orderID:<br>";
$search_1 = PurchasesSearch($conn, $orderID_1);
$search_2 = PurchasesSearch($conn, $orderID_1);
echo "Purchase1 [$orderID_1] search: ".$search_1['orderID']."<br>";
echo "Purchase2 [$orderID_2] search: ".$search_2['orderID']."<br>";

echo "<br>Test Delete:<br>";
echo "Purchase1 [$orderID_1] delete: ".PurchasesDelete($conn, $orderID_1)."<br>";
echo "Purchase2 [$orderID_2] delete: ".PurchasesDelete($conn, $orderID_2)."<br>";
echo "Done!";
