<!-- Admin - list all categories  -->
<?php
require_once 'model/category.php';
$categories = listAllCategory();
?>

<!DOCTYPE html>
<html>
<head>
<title>List Category</title>
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
        <h1>List Category</h1>
        <hr>
        <a href="createCategory.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Create Category</button>
        </a>
         <a href="homePageAdmin.php" class="btn">
            <button type="button" class="btn btn-sm btn-outline-secondary">Back to homepage</button>
        </a>
        <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Category Name</th>
                  <th>Action</th>
                </tr>
              </thead>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?php echo $category["name"]; ?></td>
                <td>
                    <a href="updateCategory.php?categoryId=<?php echo $category["id"]; ?>" class="btn">
                        <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                    </a>
                    <a href="deleteCategory.php?categoryId=<?php echo $category["id"]; ?>" class="btn confirmation">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script type="text/javascript">
        $('.confirmation').on('click', function () {
            return confirm('Are you sure to delete the category?');
        });
    </script>
</body>
</html>