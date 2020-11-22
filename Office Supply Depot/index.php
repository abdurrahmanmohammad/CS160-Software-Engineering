<?php
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
               <h3>Hours of Operation:</h3>
               <p>Mon - Fri 8:00 AM to 5:00 PM PCT</p>
            </div>
            <div class="col-sm-4">
               <h3>Business Location</h3>
               <p>1 Washington Sq, San Jose, CA 95192</p>
            </div>
         </div>
      </div>
   </body>
</html>
_END;
