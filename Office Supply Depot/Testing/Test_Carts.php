<?php
/** Import methods */
require_once '../Data Layer/CartMethods.php';
require_once '../Data Layer/DatabaseMethods.php';

/** Set up connection to DB */
$conn = getConnection();

echo "<h1>#################### Carts Test ####################: </h1>";

/** Initialize table */
echo "<h2>Test Initialize: ".InitializeCarts($conn)."</h2>";

/** Test Delete */
echo "<h2>Test Delete: </h2>";
echo "Cart 1 deleted: ".DeleteCart($conn, "1")."<br>";
echo "Cart 2 deleted: ".DeleteCart($conn, "2")."<br>";
echo "Cart 3 deleted: ".DeleteCart($conn, "3")."<br>";

/** Test Insert */
echo "<h2>Test Insert:</h2>";
echo "Cart 1:<br>";
echo "Cart 1 inserted item 1: ".InsertCart($conn, "1", "itm1", 1)."<br>";
echo "Cart 1 inserted item 2: ".InsertCart($conn, "1", "itm2", 2)."<br>";
echo "Cart 1 inserted item 3: ".InsertCart($conn, "1", "itm3", 3)."<br>";
echo "<br>Cart 2:<br>";
echo "Cart 2 inserted item 1: ".InsertCart($conn, "2", "itm1", 1)."<br>";
echo "Cart 2 inserted item 2: ".InsertCart($conn, "2", "itm2", 2)."<br>";
echo "Cart 2 inserted item 3: ".InsertCart($conn, "2", "itm3", 3)."<br>";
echo "<br>Cart 3:<br>";
echo "Cart 3 inserted item 1: ".InsertCart($conn, "3", "itm1", 1)."<br>";
echo "Cart 3 inserted item 2: ".InsertCart($conn, "3", "itm2", 2)."<br>";
echo "Cart 3 inserted item 3: ".InsertCart($conn, "3", "itm3", 3)."<br>";

/** Test Exists */
echo "<h2>Test Exists:</h2>";
echo "Cart 1 exists: ".!is_null(SearchCart($conn, "1"))."<br>";
echo "Cart 2 exists: ".!is_null(SearchCart($conn, "2"))."<br>";
echo "Cart 3 exists: ".!is_null(SearchCart($conn, "3"))."<br>";

/** Test Search*/
echo "<h2>Test Search: </h2>";
echo "Cart 1:<br>";
$search = SearchCart($conn, "1");
foreach($search as $cart) echo "Cart($cart[0], $cart[1], $cart[2])<br>";
echo "<br>Cart 2:<br>";
$search = SearchCart($conn, "2");
foreach($search as $cart) echo "Cart($cart[0], $cart[1], $cart[2])<br>";
echo "<br>Cart 3:<br>";
$search = SearchCart($conn, "3");
foreach($search as $cart) echo "Cart($cart[0], $cart[1], $cart[2])<br>";


/** Test Update */
echo "<h2>Test Update: </h2>";
echo "Cart 1:<br>";
echo "Update item 1: ".UpdateCart($conn, "1", "itm1", 4)."<br>";
echo "Update item 2: ".UpdateCart($conn, "1", "itm2", 5)."<br>";
echo "Update item 3: ".UpdateCart($conn, "1", "itm3", 6)."<br>";

echo "Cart 2:<br>";
echo "Update item 1: ".UpdateCart($conn, "2", "itm1", 4)."<br>";
echo "Update item 2: ".UpdateCart($conn, "2", "itm2", 5)."<br>";
echo "Update item 3: ".UpdateCart($conn, "2", "itm3", 6)."<br>";

echo "Cart 3:<br>";
echo "Update item 1: ".UpdateCart($conn, "3", "itm1", 4)."<br>";
echo "Update item 2: ".UpdateCart($conn, "3", "itm2", 5)."<br>";
echo "Update item 3: ".UpdateCart($conn, "3", "itm3", 6)."<br>";

/** Test Delete item */
echo "<h2>Test Delete Cart: </h2>";
echo "Cart 1 itm1 deleted: ".DeleteCartItem($conn, "1", "itm1")."<br>";
echo "Cart 2 itm2 deleted: ".DeleteCartItem($conn, "2", "itm2")."<br>";
echo "Cart 3 itm3 deleted: ".DeleteCartItem($conn, "3", "itm3")."<br>";

echo "Cart 1:<br>";
$search = SearchCart($conn, "1");
foreach($search as $cart) echo "Cart($cart[0], $cart[1], $cart[2])<br>";
echo "<br>Cart 2:<br>";
$search = SearchCart($conn, "2");
foreach($search as $cart) echo "Cart($cart[0], $cart[1], $cart[2])<br>";
echo "<br>Cart 3:<br>";
$search = SearchCart($conn, "3");
foreach($search as $cart) echo "Cart($cart[0], $cart[1], $cart[2])<br>";

/** Test Delete Cart */
echo "<br>Test Delete Cart: <br>";
echo "Cart 1 deleted: ".DeleteCart($conn, "1")."<br>";
echo "Cart 2 deleted: ".DeleteCart($conn, "2")."<br>";
echo "Cart 3 deleted: ".DeleteCart($conn, "3")."<br>";

echo "Done!";