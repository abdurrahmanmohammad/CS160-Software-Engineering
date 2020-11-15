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
        <h1 class="display-3">Check out</h1>
        <hr>
        <div id="shopping-cart">
            <h1 class="display-4">Order infomation</h1>
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
                        <td><?php echo $item["name"]; ?></td>
                        </td>
                        <td><?php echo $item["code"]; ?></td>
                        <td><?php echo $item["quantity"]; ?></td>
                        <td><?php echo "$ ".$item["price"]; ?></td>
                        <td><?php echo "$ ".number_format($item_price,2); ?></td>
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
        <br>
        <form>
            <div id="shippingAddr">
                <h1 class="display-4">Shipping Address</h1>
                <div class="form-group">
                    <label for="inputShippingAddress">Address</label>
                    <input type="text" class="form-control" id="inputShippingAddress" placeholder="">
                </div>
                <div class="form-group">
                    <label for="inputShippingAddress2">Address 2</label>
                    <input type="text" class="form-control" id="inputShippingAddress2"
                        placeholder="Apartment, studio, or floor">
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputShippingCity">City</label>
                        <input type="text" class="form-control" id="inputShippingCity">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputShippingState">State</label>
                        <select id="inputShippingState" class="form-control">
                            <option>Choose...</option>
                            <option>CA</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputShippingZip">Zip</label>
                        <input type="text" class="form-control" id="inputShippingZip">
                    </div>
                </div>
            </div>
            <br>
            <div id="paymentInfo">
                <h1 class="display-4">Payment information</h1>
                <div class="form-group">
                    <label for="inputCardHolderName">Card Holder Name</label>
                    <input type="text" class="form-control" id="inputCardHolderName" placeholder="">
                </div>
                <div class="form-group">
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCardNumber">Card number</label>
                        <input type="text" class="form-control" id="inputCardNumber" placeholder="">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputCVS">CVS</label>
                        <input type="password" class="form-control" id="inputCVS">
                    </div>
                </div>
            </div>
            <br>
            <div id="billingAddr">
                <h1 class="display-4">Shipping Address</h1>
                <div class="form-group">
                    <label for="inputShippingAddress">Address</label>
                    <input type="text" class="form-control" id="inputShippingAddress" placeholder="">
                </div>
                <div class="form-group">
                    <label for="inputShippingAddress2">Address 2</label>
                    <input type="text" class="form-control" id="inputShippingAddress2"
                        placeholder="Apartment, studio, or floor">
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputShippingCity">City</label>
                        <input type="text" class="form-control" id="inputShippingCity">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputShippingState">State</label>
                        <select id="inputShippingState" class="form-control">
                            <option>Choose...</option>
                            <option>CA</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputShippingZip">Zip</label>
                        <input type="text" class="form-control" id="inputShippingZip">
                    </div>
                </div>
            </div>
            <br>
            <div id="shipOptions">
                <h1 class="display-4">Shipping options</h1>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox1" value="shipOption1">
                    <label class="form-check-label" for="shipCheckbox1">Pick up at Warehouse 1</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox2" value="shipOption2">
                    <label class="form-check-label" for="shipCheckbox2">Pick up at Warehouse2</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox3" value="shipOption3">
                    <label class="form-check-label" for="shipCheckbox3">Free same day shipping with drone(any orders over $100.00, 15lb less)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox4" value="shipOption4">
                    <label class="form-check-label" for="shipCheckbox4">Free 2 days shipping with truck(any orders over $100.00, 15lb more)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox5" value="shipOption5">
                    <label class="form-check-label" for="shipCheckbox5">$25 same day shipping with truck(any orders over $100.00, 15lb more)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox6" value="shipOption6">
                    <label class="form-check-label" for="shipCheckbox6">Free 2 days shipping with track(any orders over $100.00, 15lb more)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox7" value="shipOption7">
                    <label class="form-check-label" for="shipCheckbox7">$20 shipping with drone(any orders under $100.00)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shipCheckbox8" value="shipOption8">
                    <label class="form-check-label" for="shipCheckbox8">$20 shipping with truck(any orders under $100.00)</label>
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
        <br>
    </div>
</body>

</html>