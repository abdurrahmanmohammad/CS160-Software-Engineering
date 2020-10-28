<!DOCTYPE html>
<html>

<head>
    <title>Item view</title>
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
        <h1>Item View</h1>
        <hr>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Some Category</a></li>
                <li class="breadcrumb-item active" aria-current="page">Some prodct name</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col">
                <div id="itemImageCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#itemImageCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#itemImageCarousel" data-slide-to="1"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="./images/itemImage1.jpg" alt="First slide">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="./images/itemImage2.jpg" alt="Second slide">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#itemImageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#itemImageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="col">
                <h2>Some product name</h2>
                <p>Price: 1</p>
                <p>Avaliable warehouse: 1</p>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Size</th>
                            <th scope="col">Weight</th>
                            <th scope="col">xxx</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>some Category</td>
                            <td>10*10*10</td>
                            <td>1lb</td>
                            <td>xxx</td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary">Add to cart</button>
            </div>
        </div>
        <hr>
        <h3>Description</h3>

        <p>some Description</p>
    </div>
</body>

</html>a