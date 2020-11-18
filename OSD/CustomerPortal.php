<?php
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/ItemMethods.php';
require_once './Data Layer/PictureMethods.php';
require_once './Data Layer/CategoryMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "admin") === 0) header("Location: AdminPortal.php"); // Admin cannot use customer portal
else if(strcmp($account['accountType'], "customer") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();


echo <<<_END
<html>

<head>
    <title>Item view</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>

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

$categories = getCategories($conn);
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
$items = ItemSearch($conn, null, null, null, null, null); // Get all items
foreach($items as $item) {
	$picture = PictureSearch($conn, $item['itemID'])[0]['directory']; // Get first picture of item
	echo <<<_END
                        <form action="ItemView.php" method="post" enctype='multipart/form-data'>
                        <a href="javascript:;" onclick="document.getElementById('form1').submit();">
						   <input type="hidden" id="itemID" name="itemID" value="{$item['itemID']}">
						   <div class="col mb-4">
						      <div class="card h-100" style="width: 15rem;">
						            <img src="$picture" class="card-img-top" alt="Picture Unavailable" height="100">
                                    <div class="card-body">
						               <button type="submit" class="btn btn-primary">{$item['title']}<br>{$item['price']}</button>
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
	$items = CategoryGetItemsByCategory($conn, $categoryName); // Get items with the same category
	echo <<<_END
	<div class="tab-pane fade" id="$categoryName" role="tabpanel" aria-labelledby="$categoryName-tab">
	   <h1 class="display-4">$categoryName</h1>
	   <div class="row row-cols-1 row-cols-md-3">
	_END;

	foreach($items as $item) {
		$picture = PictureSearch($conn, $item['itemID'])[0]['directory']; // Get first picture of item
		echo <<<_END
		<form action="ItemView.php" method="post" enctype='multipart/form-data'>
		<input type="hidden" id="itemID" name="itemID" value="{$item['itemID']}">
			<div class="col mb-4">
				<div class="card h-100">
					<img src="$picture" class="card-img-top" alt="Picture Unavailable" height="100">
					<div class="card-body">
						<button type="submit" class="btn btn-primary">{$item['title']} \n\${$item['price']}</button>
					</div>
				</div>
			</div>
		</form>
		_END;
	}

	echo <<<_END
	   </div>
	</div>
	_END;
}

