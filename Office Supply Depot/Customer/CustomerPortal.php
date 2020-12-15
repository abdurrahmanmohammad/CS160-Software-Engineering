<?php
require_once '../Data Layer/DatabaseMethods.php';
require_once '../Data Layer/ItemMethods.php';
require_once '../Data Layer/PictureMethods.php';
require_once '../Data Layer/CategoryMethods.php';

/** Authenticate user on page */
$account = authenticate('customer'); // Retrieve user account from session

/** Set up connection to DB */
$conn = getConnection();


echo <<<_END
<html>
<head>
    <title>Item view</title>
<link rel="stylesheet" href="../CSS/bootstrap.min.css">
<script src="../JS/jquery-3.2.1.slim.min.js"></script>
<script src="../JS/popper.min.js"></script>
<script src="../JS/bootstrap.min.js"></script>
<script src="../JS/jquery-1.10.2.js"></script>
</head>

<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_customer.html", function(data) {
        $("#nav-placeholder").replaceWith(data);
    });
    </script>

    <div class="container">
        <h1 class="display-3">Customer Portal</h1>
        <hr>
        <div class="row">
            <div class="col-3">
                <div class="accordion" id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                    data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Categories
                                </button>
                            </h2>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                            data-parent="#accordion">
                            <div class="card-body">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
									<a class="nav-link active" id="AllProducts-tab" data-toggle="pill" href="#AllProducts" role="tab" aria-controls="AllProducts" aria-selected="true">All Products</a>
_END;

$categories = GetAllCategories($conn);
foreach($categories as $category) {
	$categoryName = $category['category'];
	echo <<<_END
                                	<a class="nav-link" id="$categoryName-tab" data-toggle="pill" href="#$categoryName" role="tab" aria-controls="$categoryName" aria-selected="false">$categoryName</a>
_END;
}
echo <<<_END
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="tab-content" id="v-pills-tabContent">
                
                <div class="tab-pane fade show active" id="AllProducts" role="tabpanel" aria-labelledby="AllProducts-tab">
                    <h1 class="display-4">All Products</h1>
                    <div class="row row-cols-1 row-cols-md-3">
_END;
$items = GetAllItems($conn); // Get all items
foreach($items as $item) {
	$picture = SearchPicture($conn, $item['itemID'])[0]['directory']; // Get first picture of item
	echo <<<_END
	<form action="ItemView.php" method="post" enctype='multipart/form-data'>
		<a href="ItemView.php?itemID={$item['itemID']}" onclick="document.getElementById('form1').submit();">
			<input type="hidden" id="itemID" name="itemID" value="{$item['itemID']}">
				<div class="col mb-4">
					<div class="card" style="width: 15rem;">
						<img src="$picture" class="card-img-top" alt="Picture Unavailable" style="height: 12rem;">
                  			<div class="card-body">
	                       		<h5 class="card-title">{$item['title']}</h5>
                            	<h5 class="card-title">\${$item['price']}</h5>
						    </div>
					</div>
                </div>
        </a>
	</form>
_END;
}


echo <<<_END
                    </div>
                </div>
_END;

foreach($categories as $category)
	PrintCard($conn, $category); // Pass in distinct categories
echo <<<_END
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
_END;


function PrintCard($conn, $category) {
	$categoryName = $category['category'];
	$items = GetItemsByCategories($conn, $categoryName); // Get items with the same category
	echo <<<_END
	<div class="tab-pane fade" id="$categoryName" role="tabpanel" aria-labelledby="$categoryName-tab">
	   <h1 class="display-4">$categoryName</h1>
	   <div class="row row-cols-1 row-cols-md-3">
	_END;

	foreach($items as $item) {
		$picture = SearchPicture($conn, $item['itemID'])[0]['directory']; // Get first picture of item
		echo <<<_END
		<form action="ItemView.php" method="post" enctype='multipart/form-data'>
		   <a href="ItemView.php?itemID={$item['itemID']}" onclick="document.getElementById('form1').submit();">
		      <input type="hidden" id="itemID" name="itemID" value="{$item['itemID']}">
		      <div class="col mb-4">
		         <div class="card" style="width: 15rem;">
		            <img src="$picture" class="card-img-top" alt="Picture Unavailable" style="height: 12rem;">
		            <div class="card-body">
		               <h5 class="card-title">{$item['title']}</h5>
		               <h5 class="card-title">\${$item['price']}</h5>
		            </div>
		         </div>
		      </div>
		   </a>
		</form>
		_END;
	}
	echo <<<_END
	   </div>
	</div>
	_END;
}

