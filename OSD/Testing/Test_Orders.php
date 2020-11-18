<?php

/** Import methods */
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseMethods.php'; // Directory of file
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/OrderMethods.php';

/** Set up connection to DB */
$conn = getConnection();

echo "Test generate orderID: <br>";
$orderID_1 = GenerateOrderID($conn); // Generate an orderID
$orderID_2 = GenerateOrderID($conn); // Generate an orderID
echo "OrderID 1: ".$orderID_1."<br>";
echo "OrderID 2: ".$orderID_2."<br>";

echo "<br>Test Insert: <br>";
$email = "testing@test.com";
echo "Order1 [$orderID_1] insert: ".OrdersInsert($conn, $orderID_1, $email, 12.0, "Option5", "3003 Scott Blvd, Santa Clara, CA 95054")."<br>";
echo "Order2 [$orderID_1] insert: ".OrdersInsert($conn, $orderID_2, $email, 12.0, "Option5", "3003 Scott Blvd, Santa Clara, CA 95054")."<br>";


echo "<br>Test Exists:<br>";
echo "Order1 [$orderID_1] exists: ".OrderExists($conn, $orderID_1)."<br>";
echo "Order2 [$orderID_2] exists: ".OrderExists($conn, $orderID_2)."<br>";

echo "<br>Test search by orderID:<br>";
$search_1 = OrdersSearchByOrdersID($conn, $orderID_1);
$search_2 = OrdersSearchByOrdersID($conn, $orderID_1);
echo "Order1 [$orderID_1] search: ".$search_1['orderID']."<br>";
echo "Order2 [$orderID_2] search: ".$search_2['orderID']."<br>";

echo "<br>Test search by email:<br>";
$search_3 = OrdersSearchByEmail($conn, $email);
echo "Order1 [$orderID_1] search: ".$search_3[0]['orderID']."<br>";
echo "Order2 [$orderID_2] search: ".$search_3[1]['orderID']."<br>";

echo "<br>Test Delete:<br>";
echo "Order1 [$orderID_1] delete: ".OrdersDelete($conn, $orderID_1)."<br>";
echo "Order2 [$orderID_2] delete: ".OrdersDelete($conn, $orderID_2)."<br>";
echo "Done!";

