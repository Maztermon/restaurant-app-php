<!DOCTYPE html>
<html>

<head>
    <title>Restaurants List</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <style>
        #map {
            height: 100%;
        }

        html,
        body {
            height: 100%;
            margin: e;
            padding: 0;
        }

        body {
            padding: 0 !important;
        }

        table {
            font-size: 14px;
        }

        .Restaurant-search {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            background: #fff;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            left: 60px;
            position: absolute;
            top: 10px;
            width: 440px;
            height: 20px;
            z-index: 1;
        }

        #map {
            margin-top: 40px;
            width: 80%;
            height: 100%;
        }

        #listing {
            position: absolute;
            width: 200px;
            height: 100%;
            overflow: auto;
            left: 1200px;
            top: 40px;
            cursor: pointer;
            overflow-x: hidden;
        }

        #findRestaurants {
            font-size: 14px;
        }

        #locationField {
            -webkit-box-flex: 1 1 190px;
            -ms-flex: 1 1 190px;
            flex: 1 1 190px;
            margin: 0 8px;
        }

        #controls {
            -webkit-box-flex: 1 1 140px;
            -ms-flex: 1 1 140px;
            flex: 1 1 140px;
        }

        #autocomplete {
            width: 100%;
        }

        #country {
            width: 100%;
        }

        .placeIcon {
            width: 20px;
            height: 34px;
            margin: 4px;
        }

        .restaurantIcon {
            width: 24px;
            height: 24px;
        }

        #resultsTable {
            border-collapse: collapse;
            width: 240px;
        }

        #rating {
            font-size: 13px;
            font-family: Arial Unicode MS;
        }

        .iw_table_row {
            height: 18px;
        }

        .iw_attribute_name {
            font-weight: bold;
            text-align: right;
        }

        .iw_table_icon {
            text-align: right;
        }
    </style>

</head>

<body>

    <div class="Restaurant-search">
        <div id="findRestaurants"> Restaurants in :</div>

        <div id="locationField">
            <input id="autocomplete" placeholder="Enter a city" type="text" />
        </div>

    </div>

    <div id="map"></div>

    <div id="listing">
        <table id="resultsTable">
            <tbody id="results"></tbody>
        </table>
    </div>

    <div style="display: none">
        <div id="info-content">
            <table>
                <tr id="iw-url-row" class="iw_table_row">
                    <td id="iw-icon" class="iw_table_icon"></td>
                    <td id="iw-url"></td>
                </tr>
                <tr id="iw-address-row" class="iw_table_row">
                    <td class="iw_attribute_name">Address:</td>
                    <td id="iw-address"></td>
                </tr>
                <tr id="iw-phone-row" class="iw_table_row">
                    <td class="iw_attribute_name">Telephone:</td>
                    <td id="iw-phone"></td>
                </tr>
                <tr id="iw-rating-row" class="iw_table_row">
                    <td class="iw_attribute_name">Rating:</td>
                    <td id="iw-rating"></td>
                </tr>
                <tr id="iw-website-row" class="iw_table_row">
                    <td class="iw_attribute_name">Website:</td>
                    <td id="iw-website"></td>
                </tr>
            </table>
        </div>
    </div>


    <script>
        let map;
        let places;
        let infoWindow;
        let markers = [];
        let autocomplete;
        const countryRestrict = {
            country: "TH"
        };
        const MARKER_PATH =
            "https://developers.google.com/maps/documentation/javascript/images/marker_green";
        const hostnameRegexp = new RegExp("^https?://.+?/");
        const countries = {

            TH: {
                center: {
                    lat: 13.8202945,
                    lng: 100.5296856
                },
                zoom: 14,
            },

        };

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: countries["TH"].zoom,
                center: countries["TH"].center,
                mapTypeControl: false,
                panControl: false,
                zoomControl: false,
                streetViewControl: false,
            });
            infoWindow = new google.maps.InfoWindow({
                content: document.getElementById("info-content"),
            });
           
            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("autocomplete"), {
                    types: ["(cities)"],
                    componentRestrictions: countryRestrict,
                    fields: ["geometry"],
                }
            );
            places = new google.maps.places.PlacesService(map);
            autocomplete.addListener("place_changed", onPlaceChanged);
            
            document
                .getElementById("country")
                .addEventListener("change", setAutocompleteCountry);
        }

       
        function onPlaceChanged() {
            const place = autocomplete.getPlace();

            if (place.geometry && place.geometry.location) {
                map.panTo(place.geometry.location);
                map.setZoom(15);
                search();
            } else {
                document.getElementById("autocomplete").placeholder = "Bang Sue";
            }
        }

       
        function search() {
            const search = {
                bounds: map.getBounds(),
                types: ["restaurant", "food"],
            };

            places.nearbySearch(search, (results, status, pagination) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && results) {
                    clearResults();
                    clearMarkers();

                  
                    for (let i = 0; i < results.length; i++) {
                        const markerLetter = String.fromCharCode("A".charCodeAt(0) + (i % 26));
                        const markerIcon = MARKER_PATH + markerLetter + ".png";

                        
                        markers[i] = new google.maps.Marker({
                            position: results[i].geometry.location,
                            animation: google.maps.Animation.DROP,
                            icon: markerIcon,
                        });
                        
                        markers[i].placeResult = results[i];
                        google.maps.event.addListener(markers[i], "click", showInfoWindow);
                        setTimeout(dropMarker(i), i * 100);
                        addResult(results[i], i);
                    }
                }
            });
        }

        function clearMarkers() {
            for (let i = 0; i < markers.length; i++) {
                if (markers[i]) {
                    markers[i].setMap(null);
                }
            }

            markers = [];
        }

       
        function setAutocompleteCountry() {
            const country = document.getElementById("country").value;

            if (country == "TH") {
                autocomplete.setComponentRestrictions({
                    country: []
                });
                map.setCenter({
                    lat: 13.8202945,
                    lng: 100.5296856
                });
                map.setZoom(14);
            } else {
                autocomplete.setComponentRestrictions({
                    country: country
                });
                map.setCenter(countries[country].center);
                map.setZoom(countries[country].zoom);
            }

            clearResults();
            clearMarkers();
        }

        function dropMarker(i) {
            return function() {
                markers[i].setMap(map);
            };
        }

        function addResult(result, i) {
            const results = document.getElementById("results");
            const markerLetter = String.fromCharCode("A".charCodeAt(0) + (i % 26));
            const markerIcon = MARKER_PATH + markerLetter + ".png";
            const tr = document.createElement("tr");

            tr.style.backgroundColor = i % 2 === 0 ? "#F0F0F0" : "#FFFFFF";
            tr.onclick = function() {
                google.maps.event.trigger(markers[i], "click");
            };

            const iconTd = document.createElement("td");
            const nameTd = document.createElement("td");
            const icon = document.createElement("img");

            icon.src = markerIcon;
            icon.setAttribute("class", "placeIcon");
            icon.setAttribute("className", "placeIcon");

            const name = document.createTextNode(result.name);

            iconTd.appendChild(icon);
            nameTd.appendChild(name);
            tr.appendChild(iconTd);
            tr.appendChild(nameTd);
            results.appendChild(tr);
        }

        function clearResults() {
            const results = document.getElementById("results");

            while (results.childNodes[0]) {
                results.removeChild(results.childNodes[0]);
            }
        }

        
        function showInfoWindow() {
           
            const marker = this;

            places.getDetails({
                    placeId: marker.placeResult.place_id
                },
                (place, status) => {
                    if (status !== google.maps.places.PlacesServiceStatus.OK) {
                        return;
                    }

                    infoWindow.open(map, marker);
                    buildIWContent(place);
                }
            );
        }

        
        function buildIWContent(place) {
            document.getElementById("iw-icon").innerHTML =
                '<img class="restaurantIcon" ' + 'src="' + place.icon + '"/>';
            document.getElementById("iw-url").innerHTML =
                '<b><a href="' + place.url + '">' + place.name + "</a></b>";
            document.getElementById("iw-address").textContent = place.vicinity;
            if (place.formatted_phone_number) {
                document.getElementById("iw-phone-row").style.display = "";
                document.getElementById("iw-phone").textContent =
                    place.formatted_phone_number;
            } else {
                document.getElementById("iw-phone-row").style.display = "none";
            }

            
            if (place.rating) {
                let ratingHtml = "";

                for (let i = 0; i < 5; i++) {
                    if (place.rating < i + 0.5) {
                        ratingHtml += "&#10025;";
                    } else {
                        ratingHtml += "&#10029;";
                    }

                    document.getElementById("iw-rating-row").style.display = "";
                    document.getElementById("iw-rating").innerHTML = ratingHtml;
                }
            } else {
                document.getElementById("iw-rating-row").style.display = "none";
            }

            
            if (place.website) {
                let fullUrl = place.website;
                let website = String(hostnameRegexp.exec(place.website));

                if (!website) {
                    website = "http://" + place.website + "/";
                    fullUrl = website;
                }

                document.getElementById("iw-website-row").style.display = "";
                document.getElementById("iw-website").textContent = website;
            } else {
                document.getElementById("iw-website-row").style.display = "none";
            }
        }

        window.initMap = initMap;
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap&libraries=places&v=weekly" defer></script>
</body>

</html>