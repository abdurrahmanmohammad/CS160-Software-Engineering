<!-- Admin - create a new user  -->
<?php
require 'model/User.php';
if (isset($_POST['username']) ){
    $userData["username"] = $_POST["username"];
    $userData["password"] = $_POST["password"];
    $userData["role"] = $_POST["role"];
    $userData["fullName"] = $_POST["fullName"];
    $userData["address"] = $_POST["address"];
    
    $duplicateUsername = false;
    if(checkUsernameExist($userData["username"])){
        $duplicateUsername = true;
    }else{
        insertUser($userData);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create User</title>
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
        <h1>Create User</h1>
    <hr>
    <?php if (isset($_POST['username']) && $duplicateUsername): ?>
    <div class="alert alert-primary" role="alert">
        Username has been used, please choose another name!
    </div>
    <?php endif; ?>
    <?php if (isset($_POST['username']) && !$duplicateUsername): ?>
    <div class="alert alert-primary" role="alert">
        User saved
    </div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="userName">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Enter user name" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" name="role" required>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" class="form-control" name="fullName" placeholder="Enter full name" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" placeholder="Enter address" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="listUser.php" class="btn">
                <button type="button" class="btn btn-primary">Back to user list</button>
            </a>
        </div>
    </form>
    </div>
    
</body>
</html>