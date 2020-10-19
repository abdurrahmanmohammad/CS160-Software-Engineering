<!-- Write your comments here -->
<!-- Create item page - link in navbar, enter item name, ... 
    (check database attributes and include them as modifable fields) -->

<!DOCTYPE html>
<html>
<head>
<title>Create Item</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
    <div id="nav-placeholder"></div>
    <script>
    $.get("./nav.html", function(data){
        $("#nav-placeholder").replaceWith(data);
    });
    </script>
    <div class="container">
        <h1>Create Item</h1>
    <hr>
    <form>
        <div class="form-group">
            <label for="productName">Product Name</label>
            <input type="text" class="form-control" id="productNameInput" aria-describedby="productNameHelp" placeholder="Enter product name" required>
        </div>
        <div class="form-group">
            <label for="productAmount">Product Amount</label>
            <input type="number" class="form-control" id="productAmountInput" placeholder="Amount" required>
        </div>
        <div class="form-group">
            <label for="uploadProductImage1">Upload Product Image 1</label>
            <input type="file" class="form-control-file" id="productImageUpload1" required>
        </div>
        <div class="form-group">
            <label for="uploadProductImage2">Upload Product Image 2</label>
            <input type="file" class="form-control-file" id="productImageUpload2">
        </div>
        <div class="form-group">
            <label for="productWarehouse">Product warehoue locate</label>
            <select class="form-control" id="productWarehouse" required>
              <option value = "1">Warehouse 1</option>
              <option value = "2">Warehouse 2</option>
            </select>
          </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    </div>
    
</body>
</html>