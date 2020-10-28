<!-- Admin - delete a user  -->
<?php
require 'model/User.php';
$userId = $_GET["userId"];
$user = deleteUser($userId);
?>

<!DOCTYPE html>
<html>
<head>
<title>Delete User</title>
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
        <h1>Delete User</h1>
    <hr>
    <div class="alert alert-primary" role="alert">
        User deleted
    </div>
    <form>
        <div class="form-group">
            <a href="listUser.php" class="btn">
                <button type="button" class="btn btn-primary">Back to user list</button>
            </a>
        </div>
    </form>
    </div>
    
</body>
</html>