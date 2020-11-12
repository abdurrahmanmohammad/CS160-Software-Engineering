<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Directory of file

/** Authenticate user on page */
$account = authenticate();

echo <<<_END
<html>
<head>
<title>Home</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.2.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_admin.html", function(data){
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Home</h1>
        <hr>
        <div class="container">
            <div class="card-deck mb-3 text-center">
            <div class="card mb-4 shadow-sm">
              <div class="card-header">
                <h4 class="my-0 font-weight-normal">Product Items</h4>
              </div>
              <div class="card-body">
                <p class="card-title"> You can create, edit, and delete a product item.</p>
                <a href="ListItems.php" class="btn">
                    <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                </a>
              </div>
            </div>
            <div class="card mb-4 shadow-sm">
              <div class="card-header">
                <h4 class="my-0 font-weight-normal">Product Categories</h4>
              </div>
              <div class="card-body">
                <p class="card-title"> You can create, edit, and delete a product category.</p>
                <a href="ListItems.php" class="btn">
                    <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                </a>
              </div>
            </div>
            <div class="card mb-4 shadow-sm">
              <div class="card-header">
                <h4 class="my-0 font-weight-normal">Users</h4>
              </div>
              <div class="card-body">
                <p class="card-title"> You can create, edit, and delete a user.</p>
                <a href="CustomerAccounts.php" class="btn">
                    <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                </a>
              </div>
            </div>
          </div>
        </div>
    </div>
    
</body>
</html>
_END;
