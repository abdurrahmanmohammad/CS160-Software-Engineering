<?php
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/ItemMethods.php';
require_once './Data Layer/PictureMethods.php';
require_once './Data Layer/CategoryMethods.php';


/** Set up connection to DB */
$conn = getConnection();

echo <<<_END

<html>
   <head>
      <title>Office Supply Depot Product Management System</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="CSS/bootstrap.min.css">
      <script src="JS/jquery-3.2.1.slim.min.js"></script>
      <script src="JS/popper.min.js"></script>
      <script src="JS/bootstrap.min.js"></script>
      <script src="JS/jquery-1.10.2.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.JS"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/JS/bootstrap.min.JS"></script>
   </head>
   <body>
      <div id="nav-placeholder"></div>
      <script>
         $.get("./nav.html", function (data) {
             $("#nav-placeholder").replaceWith(data);
         });
      </script>
      <div class="jumbotron text-center">
         <h1>Office Supply Depot</h1>
         <p>Product Management System</p>
      </div>
      <div class="container">
         <div class="row">
            <div class="col-sm-4">
               <h3>Business Information</h3>
               <p>Based in San Jose, CA. </p>
               <p>Delivers office products of all kind at a competitive price and in a timely manner.</p>
            </div>
            <div class="col-sm-4">
               <h3>Hours of Operation</h3>
               <p>Mon - Fri 8:00 AM to 5:00 PM PCT</p>
            </div>
            <div class="col-sm-4">
               <h3>Business Location</h3>
               <p>1 Washington Sq, San Jose, CA 95192</p>
            </div>
         </div>
      </div>
_END;

echo <<<_END
    <div class="container">
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
	if(!$categories) break;
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
	if(!$item) break;
	$picture = SearchPicture($conn, $item['itemID'])[0]['directory']; // Get first picture of item
	$picture = substr($picture, 1); // Remove first char
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

foreach($categories as $category) {
	if(!$categories) break;
	PrintCard($conn, $category); // Pass in distinct categories
}

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
		if(!$item) break;
		$picture = SearchPicture($conn, $item['itemID'])[0]['directory']; // Get first picture of item
		$picture = substr($picture, 1); // Remove first char
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
