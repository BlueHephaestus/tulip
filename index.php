<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" href="/tulip/new_site_design/assets/images/favicon.ico" type="image/x-icon">

  <!--Need to include this at the top so we can still use $(document).ready-->
  <script src="/tulip/new_site_design/assets/jquery-1.12.0.min.js"></script>

  <link rel="stylesheet" href="/tulip/new_site_design/assets/bootstrap.min.css">
  <link rel="stylesheet" href="/tulip/new_site_design/assets/google-open-sans.css">
  <link rel="stylesheet" href="/tulip/new_site_design/assets/css/index_style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/tulip/new_site_design/assets/css/datepicker3.css">
 
<?php 
  if (isset($_GET["state"]) && isset($_GET["city"])){
    $location_set = true;
    $state = $_GET["state"];
    $city = $_GET["city"];
  }else{
    $location_set = false;
  }
?>

  <script>
      var locations = {"South Carolina" : ["Anderson", "Central", "Clemson", "Greenville", "Seneca"], "Wisconsin" : ["Fitchburg", "Madison", "Middleton", "Verona"]};

      var state = "";
      var city = "";
      var type = "";

      function post(path, params) {
        method = "post"; // Set method to post by default if not specified..

        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);

        for(var key in params) {
          if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
           }
        }

        document.body.appendChild(form);
        form.submit();
      }

      function is_numeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
      }

      function getLocations () {
        var oReq = new XMLHttpRequest(); //New request object
        oReq.onload = function() {
          locations = JSON.parse(this.responseText);
          getStateButtons();
          getTypesContainer();
        };
        oReq.open("get", "get_locations.php", true);
        oReq.send();
      }
      
      /*function locationSet(){
        var oReq = new XMLHttpRequest(); //New request object
        oReq.onload = function() {
          locationSet = this.responseText;
        };
        oReq.open("get", "location_set.php", true);
        oReq.send();
      }
       */

      function buttonClicked(button){
        button.toggleClass("btn-clicked");
      }

      function clickButtons(container){
        var buttons = document.getElementById(container).getElementsByClassName('btn-option');
        for (var buttonIndex = 0; buttonIndex < buttons.length; buttonIndex++){
          button = buttons[buttonIndex];
          if (button.className.indexOf("btn-clicked") == -1 && button.innerText != "All"){
            button.className += " btn-clicked";
          }
        }
      }

      function unclickButtons(container){
        var buttonsClickedRaw = document.getElementById(container).getElementsByClassName('btn-clicked');//JS was holding this to the same memory
        var buttonsClicked = [];
        for (buttonClickedRaw in buttonsClickedRaw){
          buttonsClicked.push(buttonsClickedRaw[buttonClickedRaw]);
        }
        buttonsClicked = buttonsClicked.slice(0, -3);

        for (var buttonClickedIndex = 0; buttonClickedIndex < buttonsClicked.length; buttonClickedIndex++){
          button = buttonsClicked[buttonClickedIndex];
          if (button.innerText != "All"){
            button.className = button.className.replace("btn-clicked", "").trim();
          }
        }
      }
      
      function getStateButtons(){
        //Unfortunately, since these can vary in # of buttons, we have to put in the html manually
        stateContainerHTML = "";
        for (stateIndex in locations){
          stateButton = "<button type='button' align='center' class='btn-option'>" + stateIndex + "</button>";
          stateContainerHTML += stateButton;
        }
        document.getElementById('states-container').innerHTML = stateContainerHTML;
      }

      function getCityButtons(state){
        //Unfortunately, since these can vary in # of buttons, we have to put in the html manually
        //And since you can't easily append to innerHTML with JS, we add the continue button at the end as well.
        cityContainerHTML = "";
        for (cityIndex in locations[state]){
          cityButton = "<button type='button' align='center' class='btn-option'>" + locations[state][cityIndex] + "</button>";
          cityContainerHTML += cityButton;
        }
        document.getElementById('cities-container').innerHTML = cityContainerHTML;
      }

      function getTypesContainer(){
        //$("#types-container").fadeIn(0.4);
        typeContainerHTML = "<button type='button' align='center' class='btn-option'>Unfurnished</button>";
        typeContainerHTML += "<button type='button' align='center' class='btn-option'>Furnished</button>";
        document.getElementById('types-container').innerHTML = typeContainerHTML;
      }
      
  </script>

  <script>
    $(document).ready(function(){
      getLocations();
      locationSet = '<?php echo $location_set;?>';
      var currentLocationText = "<i class='fa fa-map-marker'></i> Current Location: ";
      geocoder = new google.maps.Geocoder();

      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(positionSuccess, positionError);
      }else {
        currentLocationText += "Geolocation is not supported by this browser.";
        document.getElementById("current-location-text").innerHTML = currentLocationText;
      }

      function positionSuccess(position) {
        //decodeLatLng(position.coords.latitude, position.coords.longitude));
        var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        if (geocoder){
          geocoder.geocode({'latLng': latLng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              var address = results[4].formatted_address;
              currentLocationText += address;
              if (locations){
                cityCheck(address);
              }
            }else {
              //failed to get location
              currentLocationText += "Failed to get location.";
            }
            document.getElementById("current-location-text").innerHTML = currentLocationText;
          });
        }
      }

      function positionError(error){
        currentLocationText += "Blocked.";
        document.getElementById("current-location-text").innerHTML = currentLocationText;
      }

      function decodeLatLng(lat, lng) {
        var latLng = new google.maps.LatLng(lat, lng);
        if (geocoder){
          geocoder.geocode({'latLng': latLng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              callback(results[4].formatted_address);
            } else {
              //failed to get location
              callback(false);
            }
          });
        }
      }
    
      function cityCheck(address){
        //how to make sure that it is a good city as well though
        //right now we need to not do anything if it's been $_POST'ed
        if (locationSet == false){
          city = address.split(",")[0];
          for (var state in locations){
            if (locations.hasOwnProperty(state)){
              if (locations[state].indexOf(city) > -1){
                //the city they are in is one we have listings for, input values and skip past the first two steps
                window.location.href = document.location.href + "?state=" + state + "&city=" + city;//js why you make me do this
                break;
              }
            }
          }
        }else{
          //We wouldn't have gotten here if the state/city was not supported, so we now input the values and skip
          //There are no buttons until that php finishes so we either go to next part in another way or we mess with the php
          //or we hardcode the locations in so that it does it immediately
          state = "<?php echo $state;?>";
          city = "<?php echo $city;?>";
          stateBtns = document.getElementById("states-container").getElementsByClassName("btn-option")
          for (var stateBtn = 0; stateBtn < stateBtns.length; stateBtn++){
            if (stateBtns[stateBtn].innerText.indexOf(state) > -1){
              stateBtns[stateBtn].click();
              break;
            }
          }
          cityBtns = document.getElementById("cities-container").getElementsByClassName("btn-option")
          for (var cityBtn = 0; cityBtn < cityBtns.length; cityBtn++){
            if (cityBtns[cityBtn].innerText.indexOf(city) > -1){
              cityBtns[cityBtn].click();
              break;
            }
          }
        }
      }

      $("#states-container").on("click", ".btn-option", function(){
        //unclick any other buttons in the states-container
        unclickButtons('states-container');
        buttonClicked($(this));
        state = $(this).text();

        $("#states-container").fadeTo("slow", 0.4);//fade state container
        getCityButtons(state);//make next container
      });

      $("#cities-container").on("click", ".btn-option", function(){
        unclickButtons('cities-container');
        buttonClicked($(this));
        city = $(this).text();
        $("#location-container").fadeTo("slow", 0.4);
        $("#states-container").fadeTo(700, 1);//so we have uniform fade

        window.location.href = "form.php" + "?state=" + state + "&city=" + city;
      });
      
      $("#types-container").on("click", ".btn-option", function(){
        //unclick any other buttons in the types-container
        unclickButtons('types-container');
        buttonClicked($(this));
        type = $(this).text();
        window.location.href = "form.php" + "?type=" + type;
      });
    });
  </script>
  <script>
    //this doesn't always trigger, but sometimes the above code screws up and this is needed
    window.onload = function(){
      locationSet = '<?php echo $location_set;?>';
      if (locationSet){
        state = "<?php echo $state;?>";
        city = "<?php echo $city;?>";
        stateBtns = document.getElementById("states-container").getElementsByClassName("btn-option")
        for (var stateBtn = 0; stateBtn < stateBtns.length; stateBtn++){
          if (stateBtns[stateBtn].innerText.indexOf(state) > -1){
            stateBtns[stateBtn].click();
            break;
          }
        }
        cityBtns = document.getElementById("cities-container").getElementsByClassName("btn-option")
        for (var cityBtn = 0; cityBtn < cityBtns.length; cityBtn++){
          if (cityBtns[cityBtn].innerText.indexOf(city) > -1){
            cityBtns[cityBtn].click();
            break;
          }
        }
      }
    }
  </script>
  <script>

  </script>
</head>
<body>
  <!--Location Selection-->
  <div align="center" class="container">
    
    <div id="wrapper">
      <div id="location-container">
        <!--States-->
        <div id="states-container">
        </div>
        <!--End States-->

        <!--Cities-->
        <div id="cities-container">
        </div>
        <!--End Cities-->
      </div>

      <!--Types-->
      <div id="types-container">
      </div>
      <!--End Types-->

    </div>
  </div>
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 
</body>
</html>
