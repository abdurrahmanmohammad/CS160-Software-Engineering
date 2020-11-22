<?php
/** Import methods */
require_once '../Data Layer/CategoryMethods.php';
require_once '../Data Layer/ItemMethods.php';
require_once '../Data Layer/DatabaseMethods.php';

/** Set up connection to DB */
$conn = getConnection();

echo "<h1>#################### Carts Test ####################: </h1>";

/** Initialize table */
echo "<h2>Test InitializeCategories(): ".InitializeCategories($conn)."</h2>";

/** Test Delete */
echo "<h2>Test DeleteCategoryByItemID(): </h2>";
echo "Cart 1 deleted: ".DeleteCategoryByItemID($conn, "itm1")."<br>";
echo "Cart 2 deleted: ".DeleteCategoryByItemID($conn, "itm2")."<br>";
echo "Cart 3 deleted: ".DeleteCategoryByItemID($conn, "itm3")."<br>";

echo "<h2>Test InsertCategory(): </h2>";
echo "Itm1:<br>";
echo "Category 1234 inserted: ".InsertCategory($conn, "itm1", "itm1: 1234")."<br>";
echo "Category 0000 inserted: ".InsertCategory($conn, "itm1", "itm1: 0000")."<br>";
echo "Category 1111 inserted: ".InsertCategory($conn, "itm1", "itm1: 1111")."<br>";

echo "<br>Itm2:<br>";
echo "Category 1234 inserted: ".InsertCategory($conn, "itm2", "itm1: 1234")."<br>";
echo "Category 0000 inserted: ".InsertCategory($conn, "itm2", "itm2: 0000")."<br>";
echo "Category 1111 inserted: ".InsertCategory($conn, "itm2", "itm2: 1111")."<br>";

echo "<br>Itm3:<br>";
echo "Category 1234 inserted: ".InsertCategory($conn, "itm3", "itm3: 1234")."<br>";
echo "Category 0000 inserted: ".InsertCategory($conn, "itm3", "itm3: 0000")."<br>";
echo "Category 1111 inserted: ".InsertCategory($conn, "itm3", "itm3: 1111")."<br>";

echo "<h2>Test SearchCategories():</h2>";
echo "Itm1:<br>";
$search = SearchCategories($conn, "itm1");
foreach($search as $cat) echo "Category($cat[0], $cat[1])<br>";

echo "Itm2:<br>";
$search = SearchCategories($conn, "itm2");
foreach($search as $cat) echo "Category($cat[0], $cat[1])<br>";

echo "Itm3:<br>";
$search = SearchCategories($conn, "itm3");
foreach($search as $cat) echo "Category($cat[0], $cat[1])<br>";


echo "<h2>Test DeleteCategory():</h2>";

echo "Itm1:<br>";
echo "Delete category 1234: ".DeleteCategory($conn, "itm1", "itm1: 1234")."<br>";
$search = SearchCategories($conn, "itm1");
foreach($search as $cat) echo "Category($cat[0], $cat[1])<br>";

echo "Itm2:<br>";
echo "Delete category 1234: ".DeleteCategory($conn, "itm2", "itm1: 1234")."<br>";
$search = SearchCategories($conn, "itm2");
foreach($search as $cat) echo "Category($cat[0], $cat[1])<br>";

echo "Itm3:<br>";
echo "Delete category 1234: ".DeleteCategory($conn, "itm3", "itm3: 1234")."<br>";
$search = SearchCategories($conn, "itm3");
foreach($search as $cat) echo "Category($cat[0], $cat[1])<br>";


echo "<h2>Test GetAllCategories():</h2>";
$search = GetAllCategories($conn);
foreach($search as $cat) echo "Category($cat[0])<br>";


echo "<h2>Test GetItemsByCategories():</h2>";
if(!SearchItem($conn, "itm1"))
	echo "Inserted item itm1: ".InsertItem($conn, "itm1", "Item 1", 1.0, 2.0, "...")."<br>";
$search = GetItemsByCategories($conn, "itm1: 0000");
foreach($search as $item) echo "Item($item[0], $item[1], $item[2], $item[3], $item[4], $item[5])<br>";


/** Test Delete */
echo "<h2>Test DeleteCategoryByItemID(): </h2>";
echo "Cart 1 deleted: ".DeleteCategoryByItemID($conn, "itm1")."<br>";
echo "Cart 2 deleted: ".DeleteCategoryByItemID($conn, "itm2")."<br>";
echo "Cart 3 deleted: ".DeleteCategoryByItemID($conn, "itm3")."<br>";
echo "Deleted item itm1: ".DeleteItem($conn, "itm1")."<br>";
echo "Done!";