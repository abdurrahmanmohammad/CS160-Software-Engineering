<!-- Admin - list all user  -->
<?php
require_once 'model/user.php';
$users = listAllUser();
?>

<!DOCTYPE html>
<html>
<head>
<title>List User</title>
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
        <h1>List User</h1>
        <hr>
        <a href="createUser.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Create User</button>
        </a>
         <a href="homePageAdmin.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
        <table class="table table-bordered">
              <thead>
                <tr>
                  <th>User Name</th>
                  <th>Role</th>
                  <th>Full Name</th>
                  <th>Address</th>
                  <th>Action</th>
                </tr>
              </thead>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user["username"]; ?></td>
                <td><?php echo $user["role"]; ?></td>
                <td><?php echo $user["fullName"]; ?></td>
                <td><?php echo $user["address"]; ?></td>
                <td>
                    <a href="updateUser.php?userId=<?php echo $user["id"]; ?>" class="btn">
                        <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                    </a>
                    <a href="deleteUser.php?userId=<?php echo $user["id"]; ?>" class="btn confirmation">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script type="text/javascript">
        $('.confirmation').on('click', function () {
            return confirm('Are you sure to delete the user?');
        });
    </script>
</body>
</html>