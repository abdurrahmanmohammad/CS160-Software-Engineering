<!-- Admin - update a user  -->
<?php
require 'model/User.php';
$userId = $_GET["userId"];
if (isset($_POST['fullName']) ){
    $userData["role"] = $_POST["role"];
    $userData["fullName"] = $_POST["fullName"];
    $userData["address"] = $_POST["address"];
    updateUserInfo($userId, $userData);
}
$user = getUser($userId);
?>

<!DOCTYPE html>
<html>
<head>
<title>Update User</title>
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
        <h1>Update User</h1>
    <hr>
    <?php if (isset($_POST['username'])): ?>
    <div class="alert alert-primary" role="alert">
        User saved
    </div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="userName">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Enter user name" value="<?php echo $user["username"]; ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" name="role" required>
                <option value="customer"<?php echo $user["role"] == "admin"?"selected":""; ?>>Customer</option>
                <option value="admin" <?php echo $user["role"] == "admin"?"selected":""; ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" class="form-control" name="fullName" placeholder="Enter full name" value="<?php echo $user["fullName"]; ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" placeholder="Enter address" value="<?php echo $user["address"]; ?>" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="listUser.php" class="btn">
                <button type="button" class="btn btn-primary">Back to user list</button>
            </a>
        </div>
    </form>
    </div>
    
</body>
</html>