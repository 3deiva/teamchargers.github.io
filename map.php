<?php
include "session.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Map with Directions Example</title>
    <!-- Include Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Include Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <link rel="stylesheet" type="text/css" href="home.css">
    <style>
        /* Set map container size */
        #map { height: calc(100vh - 200px); } /* Adjusted map height to fit screen better */

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        nav {
            display: flex;
            justify-content: center;
            margin-bottom: 10px; /* Added margin-bottom for space */
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px; /* Adjusted margin for space */
        }

        nav a:hover {
            text-decoration: underline; /* Underline on hover */
        }

        #navigation-buttons {
            margin-bottom: 20px;
            text-align: center;
        }

        #navigation-buttons label {
            color: #333; /* Changed label color */
            margin-right: 10px; /* Adjusted margin for label */
        }

        #navigation-buttons select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc; /* Added border for select box */
        }

        #navigation-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #fff; /* Changed button background color */
            color: #007bff; /* Changed button text color */
            border: 1px solid #007bff; /* Added border for button */
            border-radius: 5px;
            cursor: pointer;
        }

        #navigation-buttons button:hover {
            background-color: #007bff; /* Changed button background color on hover */
            color: #fff; /* Changed button text color on hover */
        }
    </style>
</head>
<body>
    <header>
        <h1>EV companion</h1>
    <nav>
        <ul>
            <li><a href="hu.php">Home</a></li>
            <li><a href="myprofile.php">Myprofile</a></li>
            <li id="page">Charging Stations</li>
            <li><a href="about.html">About Us</a></li>
            <li id="logout"><a href="session_unset.php"> Logout </a></li>
        </ul>
    </nav>
    </header>

    <div id="navigation-buttons">
        <label for="to">To:</label>
        <select id="to" onchange="calculateRoute()">
            <option value="Adyar"> Adyar </option>
            <option value="T.Nagar"> T.Nagar </option>
            <option value="Mylapore"> Mylapore </option>
            <option value="Anna Nagar"> Anna Nagar </option>
            <option value="Guindy"> Guindy </option>
        </select>
        <button onclick="navigateToNearestRegion()">Navigate to nearest station</button>
    </div>

    <div id="map"></div> <!-- Map container -->


    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Include Leaflet Routing Machine JS -->
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
        var map = L.map('map').setView([13.0827, 80.2707], 11); // Default view centered at Chennai coordinates [latitude, longitude], with zoom level 11
       L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
           attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
       }).addTo(map);

      

       var chennaiAreas = [
           { name: 'Adyar', coordinates: [13.0064, 80.2572] },
           { name: 'T.Nagar', coordinates: [13.0329, 80.2344] },
           { name: 'Mylapore', coordinates: [13.0339, 80.2707] },
           { name: 'Anna Nagar', coordinates: [13.0878, 80.2088] },
           { name: 'Guindy', coordinates: [13.0067, 80.2209] }
       ];

       // Add markers for all areas in Chennai showing their names and coordinates
       chennaiAreas.forEach(area => {
           var marker = L.marker(area.coordinates).addTo(map)
               .bindPopup(`<b>${area.name}</b><br/>Latitude: ${area.coordinates[0]}<br/>Longitude: ${area.coordinates[1]}`);
           
           marker.on('mouseover', function (e) {
               this.openPopup();
           });

           marker.on('mouseout', function (e) {
               this.closePopup();
           });

           marker.on('click', function (e){
               document.getElementById('to').value = area.name;
               calculateRoute();
           });
       });

       var routeControl;
       var AreaMarker; // Variable to hold the nearest area marker
       var userCoordinates;
       var userLat;
       var userLng;
       
       
       function navigateToNearestRegion() {
           
           if (navigator.geolocation) {
               if (routeControl) {
                   map.removeControl(routeControl); // Remove previous route if it exists
               }
               routeControl = L.Routing.control({
                   routeWhileDragging: true
               }).addTo(map);

               navigator.geolocation.getCurrentPosition(function (position) {
                   var userLat = position.coords.latitude;
                   var userLng = position.coords.longitude;
                   var userCoordinates = [userLat, userLng];
                   
                   var nearestArea = findNearestArea(userCoordinates);
                   var nearestCoordinates = nearestArea.coordinates;

                   // Update the waypoints with the new current location and nearest area
                   routeControl.setWaypoints([
                       L.latLng(userLat, userLng), // Current location coordinates
                       L.latLng(nearestArea.coordinates) // Nearest area coordinates
                   ]);

                   map.setView(userCoordinates, 13); // Set map view to user's current location with zoom level 13
                   var umarker=L.marker(userCoordinates, { icon: redIcon }).addTo(map).bindPopup('Your current location'); // Add marker for user's current location
                   
                   umarker.on('mouseover', function (e) {
                       this.openPopup();
                   });

                   umarker.on('mouseout', function (e) {
                       this.closePopup();
                   });
                   // Add marker for the nearest area
                   if (AreaMarker) {
                       map.removeLayer(AreaMarker); // Remove previous marker if it exists
                   }
                   AreaMarker = L.marker(nearestCoordinates,{ icon: redIcon }).addTo(map)
                       .bindPopup(`<b>${nearestArea.name}</b><br/>Latitude: ${nearestCoordinates[0]}<br/>Longitude: ${nearestCoordinates[1]}`);
                   
                   AreaMarker.on('mouseover', function (e) {
                       this.openPopup();
                   });

                   AreaMarker.on('mouseout', function (e) {
                       this.closePopup();
                   });
               });
           } else {
               alert('Geolocation is not supported by your browser');
           }
       }

       function findNearestArea(userCoordinates) {
           var nearestArea;
           var minDistance = Number.MAX_VALUE;

           chennaiAreas.forEach(area => {
               var areaCoordinates = area.coordinates;
               var distance = calculateDistance(userCoordinates[0], userCoordinates[1], areaCoordinates[0], areaCoordinates[1]);
               if (distance < minDistance) {
                   minDistance = distance;
                   nearestArea = area;
               }
           });

           return nearestArea;
       }

       function calculateDistance(lat1, lon1, lat2, lon2) {
           var R = 6371; // Radius of the earth in km
           var dLat = deg2rad(lat2 - lat1);
           var dLon = deg2rad(lon2 - lon1);
           var a =
               Math.sin(dLat / 2) * Math.sin(dLat / 2) +
               Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
               Math.sin(dLon / 2) * Math.sin(dLon / 2)
               ;
           var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
           var d = R * c; // Distance in km
           return d;
       }

       function deg2rad(deg) {
           return deg * (Math.PI / 180);
       }

       var redIcon = new L.Icon({
           iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
           shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
           iconSize: [25, 41],
           iconAnchor: [12, 41],
           popupAnchor: [1, -34],
           shadowSize: [41, 41]
       });


       function calculateRoute() {
           
           if (navigator.geolocation) {
               if (routeControl) {
                   map.removeControl(routeControl); // Remove previous route if it exists
               }
               routeControl = L.Routing.control({
               routeWhileDragging: true
           }).addTo(map);

       navigator.geolocation.getCurrentPosition(function (position) {
           userLat = position.coords.latitude;
           userLng = position.coords.longitude;
           userCoordinates = [userLat, userLng];
           
           var destinationName = document.getElementById('to').value;
           var destination = chennaiAreas.find(area => area.name === destinationName);

           // Update the waypoints with the new current location and selected destination
           routeControl.setWaypoints([
               L.latLng(userLat, userLng), // Current location coordinates
               L.latLng(destination.coordinates) // Destination coordinates
           ]);

           map.setView(userCoordinates, 13); // Set map view to user's current location with zoom level 13
           var umarker = L.marker(userCoordinates, { icon: redIcon }).addTo(map).bindPopup('Your current location'); // Add marker for user's current location
           
           umarker.on('mouseover', function (e) {
               this.openPopup();
           });

           umarker.on('mouseout', function (e) {
               this.closePopup();
           });

           // Add marker for the selected destination
           if (AreaMarker) {
               map.removeLayer(AreaMarker); // Remove previous marker if it exists
           }
           AreaMarker = L.marker(destination.coordinates, { icon: redIcon }).addTo(map)
               .bindPopup(`<b>${destination.name}</b><br/>Latitude: ${destination.coordinates[0]}<br/>Longitude: ${destination.coordinates[1]}`);
           
           AreaMarker.on('mouseover', function (e) {
               this.openPopup();
           });

           AreaMarker.on('mouseout', function (e) {
               this.closePopup();
           });

           // Add markers for all areas in Chennai showing their names and coordinates
chennaiAreas.forEach(area => {
    var marker = L.marker(area.coordinates).addTo(map)
        .bindPopup(`<b>${area.name}</b><br/>Latitude: ${area.coordinates[0]}<br/>Longitude: ${area.coordinates[1]}`);

    marker.on('mouseover', function (e) {
        this.openPopup();
    });

    // Add markers for all areas in Chennai showing their names and coordinates
chennaiAreas.forEach(area => {
    var marker = L.marker(area.coordinates).addTo(map)
        .bindPopup(`<b>${area.name}</b><br/>Latitude: ${area.coordinates[0]}<br/>Longitude: ${area.coordinates[1]}`);

    marker.on('mouseover', function (e) {
        this.openPopup();
    });

    marker.on('mouseout', function (e) {
        this.closePopup();
    });

    marker.on('click', function (e){
        // Redirect to another page with latitude and longitude as parameters
        window.location.href = "station.php?lat=" + area.coordinates[0] + "&lng=" + area.coordinates[1];
    });
});


    marker.on('mouseout', function (e) {
        this.closePopup();
    });

    marker.on('click', function (e){
        // Redirect to another page
        window.location.href = "station.php"; // Change "destination_page.html" to the desired page URL
    });
});

       });
   } else {
       alert('Geolocation is not supported by your browser');
   }



   


      // Function to handle marker click event
    function handleMarkerClick(area) {
        var lat = area.coordinates[0];
        var lng = area.coordinates[1];
        // Redirect to the station.php page with latitude and longitude as parameters
        window.location.href = "station.php?lat=" + lat + "&lng=" + lng;
    }

    // Add marker for each area in Chennai
    chennaiAreas.forEach(area => {
        var marker = L.marker(area.coordinates).addTo(map)
            .bindPopup(`<b>${area.name}</b><br/>Latitude: ${area.coordinates[0]}<br/>Longitude: ${area.coordinates[1]}`);

        marker.on('mouseover', function (e) {
            this.openPopup();
        });

        marker.on('mouseout', function (e) {
            this.closePopup();
        });

        // Call handleMarkerClick function when marker is clicked
        marker.on('click', function (e) {
            handleMarkerClick(area);
        });
    });

}
</script>
<footer>
        <p>&copy; 2024 EV companion. All rights reserved.</p>
    </footer>
</body>
</html>
