<!-- Admin - update a category  -->
<?php
require 'model/category.php';
$categoryId = $_GET["categoryId"];
if (isset($_POST['categoryName']) ){
    $categoryName = $_POST["categoryName"];
    updateCategory($categoryId, $categoryName);
}
$category = getCategory($categoryId);
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Category</title>
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
        <h1>Update Category</h1>
    <hr>
    <?php if (isset($_POST['categoryName'])): ?>
    <div class="alert alert-primary" role="alert">
        Category saved
    </div>
    <?php endif; ?>
    <form action="updateCategory.php?categoryId=<?php echo $category["id"]; ?>" method="POST">
        <div class="form-group">
            <label for="categoryName">Category Name</label>
            <input name="categoryName" type="text" class="form-control" id="categoryName" placeholder="Enter category name" required value ="<?php echo $category["name"]; ?>">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="listCategory.php" class="btn">
                <button type="button" class="btn btn-primary">Back to category list</button>
            </a>
        </div>
        
    </form>
    </div>
    
</body>
</html>