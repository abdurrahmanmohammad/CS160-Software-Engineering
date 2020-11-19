<?php
require_once './Data Layer/DatabaseMethods.php';
require_once './Data Layer/OrderMethods.php';

/** Authenticate user on page */
$account = authenticate(); // Retrieve user account from session
if(strcmp($account['accountType'], "customer") === 0) header("Location: CustomerPortal.php"); // Customer cannot use admin portal
else if(strcmp($account['accountType'], "admin") !== 0) header("Location: Signin.php?logout=true"); // Logout if account is not a customer

/** Set up connection to DB */
$conn = getConnection();

echo <<<_END
<!DOCTYPE html>
<html>
<head>
    <title>Drone Delivery</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBHBlsmFRD1tJK4kU8qTl9cATAtEyxXF7A&callback=initMap&libraries=&v=weekly"
            defer
    ></script>
    <style type="text/css">
        #right-panel {
            font-family: "Roboto", "sans-serif";
            line-height: 30px;
            padding-left: 10px;
        }

        #right-panel select,
        #right-panel input {
            font-size: 15px;
        }

        #right-panel select {
            width: 100%;
        }

        #right-panel i {
            font-size: 12px;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100%;
            float: left;
            width: 70%;
            height: 100%;
        }

        #right-panel {
            margin: 20px;
            border-width: 2px;
            width: 20%;
            height: 400px;
            float: left;
            text-align: left;
            padding-top: 0;
        }

        #directions-panel {
            margin-top: 10px;
            background-color: #ffee77;
            padding: 10px;
            overflow: scroll;
            height: 174px;
        }
    </style>
    <script>
        function initMap() {
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: {lat: 41.85, lng: -87.65},
            });
            directionsRenderer.setMap(map);
            document.getElementById("submit").addEventListener("click", () => {
                calculateAndDisplayRoute(directionsService, directionsRenderer);
            });
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer) {
            const waypts = [];
            const checkboxArray = document.getElementById("waypoints");

            for (let i = 0; i < checkboxArray.length; i++) {
                if (checkboxArray.options[i].selected) {
                    waypts.push({
                        location: checkboxArray[i].value,
                        stopover: true,
                    });
                }
            }
            directionsService.route(
                {
                    origin: document.getElementById("start").value,
                    destination: document.getElementById("end").value,
                    waypoints: waypts,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);
                        const route = response.routes[0];
                        const summaryPanel = document.getElementById("directions-panel");
                        summaryPanel.innerHTML = "";

                        // For each route, display summary information.
                        for (let i = 0; i < route.legs.length; i++) {
                            const routeSegment = i + 1;
                            summaryPanel.innerHTML +=
                                "<b>Route Segment: " + routeSegment + "</b><br>";
                            summaryPanel.innerHTML += route.legs[i].start_address + " to ";
                            summaryPanel.innerHTML += route.legs[i].end_address + "<br>";
                            summaryPanel.innerHTML +=
                                route.legs[i].distance.text + "<br><br>";
                        }
                    } else {
                        window.alert("Directions request failed due to " + status);
                    }
                }
            );
        }
    </script>
</head>
<body>
<div id="map"></div>
<div id="right-panel">
    <div>
        <b>Pickup Location:</b>
        <br>
        <label>1 Washington Sq, San Jose, CA 95192</label>
        <input type="hidden" id="start" value="1 Washington Sq, San Jose, CA 95192" readonly>
        <input type="hidden" id="end" value="1 Washington Sq, San Jose, CA 95192" readonly>
        <br/>
        
        <b>Orders</b> <br />
        <i>(Ctrl+Click or Cmd+Click for multiple selection)</i> <br />
        <select multiple id="waypoints">
_END;
PrintOrderChoices($conn);
echo <<<_END
        </select>
        <br><br>
        <input type="submit" id="submit"/>
    </div>
    <div id="directions-panel"></div>
</div>
</body>
</html>
_END;

function PrintOrderChoices($conn) {

	$orders = OrdersGetUndelivered($conn, 'Option3');
	foreach($orders as $order)
		echo <<<_END
		<option value="{$order['address']}">
		{$order['address']}
		</option>
		_END;
}

// Have separate page for drone
// Print same day shipping first
// Have field in orders "delivered" - make undelivered at beg in checkout, print on order history pages
// Have separate page which modifies isDelivered field 0 or 1 in orders table
// Have 2 select boxes: prioority first then 2 day

// Delivery options 3 is done by truck