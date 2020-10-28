<!-- Write your comments here -->
<!-- Cart view - cart icon on homepage navbar, 
    when clicked go to cart page which display all items in cart, 
    have select shipping list -->

<!DOCTYPE html>
<html>

<head>
    <title>Shopping Cart</title>
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
    $.get("./nav.html", function(data) {
        $("#nav-placeholder").replaceWith(data);
    });
    </script>

    <div class="container">
        <h1>Shopping Cart</h1>
        <hr>
        <div id="shopping-cart">
            <?php
            $item1 =  array(
                "name"=>"item1",
                "quantity"=>"1",
                "price"=>"10",
                "image"=>"./images/ItemImage1.jpg",
                "code"=>"001",
            );
            $item2 = array(
                "name"=>"item2",
                "quantity"=>"2",
                "price"=>"20",
                "image"=>"./images/ItemImage2.jpg",
                "code"=>"002",
            );
            $array = array($item1,$item2);
            if(isset($array)){
                $total_quantity = 0;
                $total_price = 0;
                ?>
            <table class="table">
                <tbody>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <?php
                foreach ($array as $item){
                $item_price = $item["quantity"]*$item["price"];
                ?>
                    <tr>
                        <td><img src="<?php echo $item["image"]; ?>" class="rounded" /></td>
                        <td><?php echo $item["name"]; ?></td></td>
                        <td><?php echo $item["code"]; ?></td>
                        <td><?php echo $item["quantity"]; ?></td>
                        <td><?php echo "$ ".$item["price"]; ?></td>
                        <td><?php echo "$ ".number_format($item_price,2); ?></td>
                        <td><a href="index.php?action=remove&code=<?php echo $item["code"]; ?>">
                                Remove Item
                            </a></td>
                    </tr>
                    <?php
                    $total_quantity += $item["quantity"];
                    $total_price += ($item["price"]*$item["quantity"]);
                }
                ?>
                    <tr>
                        <td></td>
                        <td>Total:</td>
                        <td><?php echo $total_quantity; ?></td>
                        <td></td>
                        <td>
                            <strong><?php echo "$ ".number_format($total_price, 2); ?></strong>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <?php
            } else {
                ?>
            <div class="no-records">Your Cart is Empty</div>
            <?php 
            }
            ?>
        </div>

    </div>
</body>

</html>