<!-- Admin - create a new category  -->

<!DOCTYPE html>
<html>
<head>
<title>Create Category</title>
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
        <h1>Create Category</h1>
    <hr>
    <form>
        <div class="form-group">
            <label for="productName">Category Name</label>
            <input type="text" class="form-control" id="categoryNameInput" placeholder="Enter category name" required>
        </div>
        <div class="form-group">
            <label for="uploadCategoryImage1">Upload Category Image 1</label>
            <input type="file" class="form-control-file" id="categoryImageUpload1" required>
        </div>
        <div class="form-group">
            <label for="uploadCategoryImage2">Upload Category Image 2</label>
            <input type="file" class="form-control-file" id="categoryImageUpload2">
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    </div>
    
</body>
</html>