<!-- Write your comments here -->
<!-- View/update item page - click item on homepage, go to this page, 
    display image and description, allows fields to be modified, 
    include a separate list of inventory table fields -->

<!DOCTYPE html>
<html>

<head>
    <title>Update Item</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>

<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav_admin.html", function(data) {
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Update Item</h1>
        <hr>
        <form>
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" class="form-control" id="productNameInput" aria-describedby="productNameHelp"
                    value="some retrived product name" placeholder="Enter product name" required>
            </div>
            <div class="form-row">
                <div class="col">
                    <div class="form-group">
                        <label for="productWeight">Product Weight</label>
                        <input type="text" class="form-control" id="productWeightInput"
                            aria-describedby="productWeightHelp" value="some retrived product Weight"
                            placeholder="Enter product Weight" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="productSize">Product Size</label>
                        <input type="text" class="form-control" id="productSizeInput" aria-describedby="productSizeHelp"
                            value="some retrived product Size" placeholder="Enter product Size" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="productCategory">Product Category</label>
                        <select class="form-control" id="productCategory" required>
                            <option value="1">Category 1</option>
                            <option value="2">Category 2</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="productDescription">Product Description</label>
                <textarea class="form-control" id="productDescription" rows="3">12345</textarea>
            </div>
            <div class="form-group">
                <label for="uploadProductImage1">Product Image 1</label>
                <img class="d-block w-10" src="./images/itemImage1.jpg">
                <input type="file" class="form-control-file" id="productImageUpload1" required>
            </div>
            <div class="form-group">
                <label for="uploadProductImage2">Product Image 2</label>
                <img class="d-block w-10" src="./images/itemImage2.jpg">
                <input type="file" class="form-control-file" id="productImageUpload2">
            </div>
            <div class="form-group">
                <label for="productWarehouse">Product warehoue locate</label>
                <select class="form-control" id="productWarehouse" required>
                    <option value="1">Warehouse 1</option>
                    <option value="2" selected>Warehouse 2</option>
                </select>
            </div>
            <div class="form-group">
                <label for="productAmount">Product Amount</label>
                <input type="number" class="form-control" id="productAmountInput" value="100" placeholder="Amount"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>