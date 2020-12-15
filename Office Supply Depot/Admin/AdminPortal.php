<?php
/** Import methods */
require_once '../Data Layer/DatabaseMethods.php';

/** Authenticate user on page */
$account = authenticate('admin'); // Retrieve user account from session

echo <<<_END
<html>
   <head>
      <title>Home</title>
      <link rel="stylesheet" href="../CSS/bootstrap.min.css">
      <script src="../JS/jquery-3.2.1.slim.min.js"></script>
      <script src="../JS/popper.min.js"></script>
      <script src="../JS/bootstrap.min.js"></script>
      <script src="../JS/jquery-1.10.2.js"></script>
   </head>
   <body>
      <div id="nav-placeholder"></div>
      <script>
         $.get("admin_navbar.html", function(data){
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
                     <h4 class="my-0 font-weight-normal">Product Management</h4>
                  </div>
                  <div class="card-body">
                     <p class="card-title"> You can create, edit, and delete a product item.</p>
                     <a href="ManageItems.php" class="btn">
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
                     <a href="ManageItems.php" class="btn">
                     <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                     </a>
                  </div>
               </div>
               <div class="card mb-4 shadow-sm">
                  <div class="card-header">
                     <h4 class="my-0 font-weight-normal">Customers</h4>
                  </div>
                  <div class="card-body">
                     <p class="card-title">View customer order history and manage accounts.</p>
                     <a href="ManageCustomers.php" class="btn">
                     <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                     </a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      
      
               <div class="container">
            <div class="card-deck mb-3 text-center">
               <div class="card mb-4 shadow-sm">
                  <div class="card-header">
                     <h4 class="my-0 font-weight-normal">Update Order Delivery</h4>
                  </div>
                  <div class="card-body">
                     <p class="card-title"> You can update the delivery status of an order.</p>
                     <a href="OrdersUndelivered.php" class="btn">
                     <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                     </a>
                  </div>
               </div>
               <div class="card mb-4 shadow-sm">
                  <div class="card-header">
                     <h4 class="my-0 font-weight-normal">Drone Delivery Routes</h4>
                  </div>
                  <div class="card-body">
                     <p class="card-title">Plan drone deliveries and view optimized routes.</p>
                     <a href="DeliveryDrone.php" class="btn">
                     <button type="button" class="btn btn-lg btn-block btn-primary">View</button>
                     </a>
                  </div>
               </div>
               <div class="card mb-4 shadow-sm">
                  <div class="card-header">
                     <h4 class="my-0 font-weight-normal">Truck Delivery Routes</h4>
                  </div>
                  <div class="card-body">
                     <p class="card-title">Plan truck deliveries and view optimized routes.</p>
                     <a href="DeliveryTruck.php" class="btn">
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