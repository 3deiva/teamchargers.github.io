<?php
include "session.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV Companion - Charging Stations</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Include Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: calc(100vh - 200px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-green-600 text-white p-6 text-center">
        <h1 class="text-2xl font-bold">EV Companion</h1>
        <nav class="mt-4">
            <ul class="flex justify-center space-x-8">
                <li><a href="hu.php" class="hover:text-yellow-300">Home</a></li>
                <li><a href="myprofile.php" class="hover:text-yellow-300">My Profile</a></li>
                <li><span class="text-blue-300">Charging Stations</span></li>
                <li><a href="about.html" class="hover:text-yellow-300">About Us</a></li>
                <li><a href="session_unset.php" class="hover:text-yellow-300">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="my-6 text-center">
        <label for="to" class="mr-2 text-gray-700">To:</label>
        <select id="to" class="p-2 border rounded" onchange="calculateRoute()">
            <option value="Adyar">Adyar</option>
            <option value="T.Nagar">T.Nagar</option>
            <option value="Mylapore">Mylapore</option>
            <option value="Anna Nagar">Anna Nagar</option>
            <option value="Guindy">Guindy</option>
        </select>
        <button onclick="navigateToNearestRegion()" class="ml-4 bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
            Navigate to Nearest Station
        </button>
    </div>

    <div id="map" class="m-6 rounded shadow-lg"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
        var map = L.map('map').setView([13.0827, 80.2707], 11); // Default view centered at Chennai coordinates
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

        chennaiAreas.forEach(area => {
            var marker = L.marker(area.coordinates).addTo(map)
                .bindPopup(`<b>${area.name}</b><br/>Latitude: ${area.coordinates[0]}<br/>Longitude: ${area.coordinates[1]}`);
            
            marker.on('mouseover', function (e) {
                this.openPopup();
            });

            marker.on('mouseout', function (e) {
                this.closePopup();
            });

            marker.on('click', function (e) {
                document.getElementById('to').value = area.name;
                calculateRoute();
            });
        });

        var routeControl;
        var AreaMarker;
        var userCoordinates;
        var userLat;
        var userLng;
       
        function navigateToNearestRegion() {
            if (navigator.geolocation) {
                if (routeControl) {
                    map.removeControl(routeControl);
                }
                routeControl = L.Routing.control({
                    routeWhileDragging: true
                }).addTo(map);

                navigator.geolocation.getCurrentPosition(function (position) {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    userCoordinates = [userLat, userLng];
                   
                    var nearestArea = findNearestArea(userCoordinates);
                    var nearestCoordinates = nearestArea.coordinates;

                    routeControl.setWaypoints([
                        L.latLng(userLat, userLng),
                        L.latLng(nearestCoordinates)
                    ]);

                    map.setView(userCoordinates, 13);
                    var umarker = L.marker(userCoordinates, { icon: redIcon }).addTo(map).bindPopup('Your current location');
                   
                    umarker.on('mouseover', function (e) {
                        this.openPopup();
                    });

                    umarker.on('mouseout', function (e) {
                        this.closePopup();
                    });

                    if (AreaMarker) {
                        map.removeLayer(AreaMarker);
                    }
                    AreaMarker = L.marker(nearestCoordinates, { icon: redIcon }).addTo(map)
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
            var R = 6371;
            var dLat = deg2rad(lat2 - lat1);
            var dLon = deg2rad(lon2 - lon1);
            var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;
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
                    map.removeControl(routeControl);
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

                    routeControl.setWaypoints([
                        L.latLng(userLat, userLng),
                        L.latLng(destination.coordinates)
                    ]);

                    map.setView(userCoordinates, 13);
                    var umarker = L.marker(userCoordinates, { icon: redIcon }).addTo(map).bindPopup('Your current location');
                   
                    umarker.on('mouseover', function (e) {
                        this.openPopup();
                    });

                    umarker.on('mouseout', function (e) {
                        this.closePopup();
                    });

                    if (AreaMarker) {
                        map.removeLayer(AreaMarker);
                    }
                    AreaMarker = L.marker(destination.coordinates, { icon: redIcon }).addTo(map)
                        .bindPopup(`<b>${destination.name}</b><br/>Latitude: ${destination.coordinates[0]}<br/>Longitude: ${destination.coordinates[1]}`);
                   
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

        function handleMarkerClick(area) {
            var lat = area.coordinates[0];
            var lng = area.coordinates[1];
            window.location.href = "station.php?lat=" + lat + "&lng=" + lng;
        }

        chennaiAreas.forEach(area => {
            var marker = L.marker(area.coordinates).addTo(map)
                .bindPopup(`<b>${area.name}</b><br/>Latitude: ${area.coordinates[0]}<br/>Longitude: ${area.coordinates[1]}`);

            marker.on('mouseover', function (e) {
                this.openPopup();
            });

            marker.on('mouseout', function (e) {
                this.closePopup();
            });

            marker.on('click', function (e) {
                handleMarkerClick(area);
            });
        });
    </script>
    <footer class="bg-green-600 text-white text-center p-4">
        <p>&copy; 2024 EV Companion. All rights reserved.</p>
    </footer>
</body>
</html>
