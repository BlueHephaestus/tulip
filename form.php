<!DOCTYPE html>
<html lang="en">
<head>
  <title>TulipPM - Short Term Furnished Housing</title>

  <meta name="keywords" content="tulip, property, management, tulippm, tulippm.com, housing, rental, furnished, unfurnished">
  <meta name="description" content="TulipPM: Helping to find the perfect home for you">
  <meta name="referrer" content="always">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <!--Have to include this at the top so we can still use $(document).ready-->
  <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans" type="text/css">
  <link rel="stylesheet" href="/tulip/new_site_design/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.min.css">
 
<?php 
  $location_set = false;
  $type_set = false;
  $error_set = false;
  if (isset($_GET["state"]) && isset($_GET["city"])){
    $location_set = true;
    $state = $_GET["state"];
    $city = $_GET["city"];
  }elseif(isset($_GET["type"])){
    $type_set = true;
    $type = $_GET["type"];
  }elseif(isset($_GET["error"])){
    $error_set = true;
    $error_id = $_GET["error"];
  }
?>

  <script>
      var locations = {"South Carolina" : ["Anderson", "Central", "Clemson", "Greenville", "Seneca"], "Wisconsin" : ["Fitchburg", "Madison", "Middleton", "Verona"]};

      var state = "";
      var city = "";
      var type = "";
      var priority = "";
      var beds = "";
      var baths = "";
      var travelFrequency = "";
      var startDate = "";
      var endDate = "";
      var nightsVisiting = [];
      var pets = [];
      var otherCities = [];
      var homeTypes = [];
      var parkingNeeds = "";
      var comments = "";
      var firstName = "";
      var lastName = "";
      var phone = "";
      var email = "";
      var budget = "";

      function post(path, params) {
        method = "post"; // Set method to post by default if not specified.

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

      function jumpTo(anchor){
        var $root = $('html, body');
        href = "#" + anchor;
        $root.animate({
          scrollTop: $(href).offset().top
        }, 500, function () {
          window.location.hash = href;
        });
      }

      function is_numeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
      }

      function getLocations () {
        var oReq = new XMLHttpRequest(); //New request object
        oReq.onload = function() {
          locations = JSON.parse(this.responseText);
          getStateButtons();
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

      /*function buttonClicked(button){
        if (button.className.indexOf("btn-clicked") > -1){
          //already clicked, set back to default state
          button.className = button.className.replace("btn-clicked", "").trim();
        }else{
          //not clicked, add clicked class
          button.className += " btn-clicked";
        }
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
        $("#types-container").fadeIn(0.4);
        typeCheck();
      }
      
      function getPriorityContainer(){
        $("#priority-container").fadeIn(0.4);
        if (type == "Furnished"){
          document.getElementById('priority-container').getElementsByClassName('btn-option')[1].innerText = "Beds";
        }else{
          document.getElementById('priority-container').getElementsByClassName('btn-option')[1].innerText = "Beds & Baths";
        }
      }

      function getBedsContainer(){
        $("#beds-container").fadeIn(0.4);
      }

      function getBathsContainer(){
        if (type == "Unfurnished"){
          $("#baths-container").fadeIn(0.4);
          jumpTo('baths-container');
        }else{
          getTravelFrequencyContainer();
        }
      }

      function getTravelFrequencyContainer(){
        if (type == "Furnished"){
          document.getElementById("travel-frequency-container").getElementsByClassName('page-header-subtitle')[0].innerHTML = "How often will you be coming to " + city + "?" + "<p class='form-required'> *</p>";
          $("#travel-frequency-container").fadeIn(0.4);
          jumpTo('travel-frequency-container');
        }else{
          getDatesContainer();
        }
      }

      function getDatesContainer(){
        $("#dates-container").fadeIn(0.4);
        jumpTo('dates-container');
        document.getElementById('dates-container').getElementsByClassName('form-control')[0].title = "When will you start travelling to " + city + "? This could be the date that your vacation or contract starts.";
        document.getElementById('dates-container').getElementsByClassName('form-control')[1].title = "When will you stop travelling to " + city + "? This could be your vacation or contract end date.";

        if (type == "Furnished"){
          document.getElementById('dates-container').getElementsByClassName('form-control')[0].placeholder = "Travel Start Date";
          document.getElementById('dates-container').getElementsByClassName('form-control')[1].placeholder = "Travel End Date";
        }else{
          document.getElementById('dates-container').getElementsByClassName('form-control')[0].placeholder = "Lease Start Date";
          document.getElementById('dates-container').getElementsByClassName('form-control')[1].placeholder = "Lease End Date";
        }
      }

      function getNightsVisitingContainer(){
        if (type == "Furnished"){
          //$('html, body').animate({ scrollTop: 1875}, 500);
          document.getElementById("nights-visiting-container").getElementsByClassName("page-header-subtitle")[0].innerHTML = "What nights will you be in " + city + "?" + "<p class='form-required'>*</p>";
          $("#nights-visiting-container").fadeIn(0.4);
          jumpTo('nights-visiting-container');
        }else{
          //$('html, body').animate({ scrollTop: 1965}, 500);
          $("#pets-container").fadeIn(0.4);
          jumpTo('pets-container');
        }
      }

      function getOtherCitiesContainer(){
        $("#other-cities-container").fadeIn(0.4);
        jumpTo('other-cities-container');
        otherCitiesContainerHTML = "";
        otherCitiesContainerHTML += "<span class='page-header-subtitle'>Other cities you would consider living in?</span>";
        for (cityIndex in locations[state]){
          if (locations[state][cityIndex] != city){//don't display the already selected city in the list of options
            cityButton = "<button type='button' align='center' class='btn-option'>" + locations[state][cityIndex] + "</button>";
            otherCitiesContainerHTML += cityButton;
          }
        }
        allButton = "<button type='button' align='center' class='btn-option'>All</button>";
        otherCitiesContainerHTML += allButton;
        continueButton = "<br><button type='button' align='center' class='btn-continue'>Continue</button>";
        otherCitiesContainerHTML += continueButton;
        document.getElementById('other-cities-container').innerHTML = otherCitiesContainerHTML;
      }

      function getHomeTypesContainer(){
        $("#home-types-container").fadeIn(0.4);
      }

      function getParkingNeedsContainer(){
        $("#parking-needs-container").fadeIn(0.4);
      }

      function getCommentsContainer(){
        $("#comments-container").fadeIn(0.4);
      }

      function getContactInfoContainer(){
        $("#contact-info-container").fadeIn(0.4);
      }

      function getBudgetContainer(){
        $("#budget-container").fadeIn(0.4);
      }

      function typeCheck(){
        //how to make sure that it is a good city as well though
        //right now we need to not do anything if it's been $_POST'ed
        if (typeSet){
          type = "<?php echo $type;?>";
          typeBtns = document.getElementById("types-container").getElementsByClassName("btn-option")
          for (var typeBtn = 0; typeBtn < typeBtns.length; typeBtn++){
            if (typeBtns[typeBtn].innerText.indexOf(type) > -1){
              typeBtns[typeBtn].click();
              break;
            }
          }
        }
      }
      function errorCheck(){
        if (errorSet){
        
        }
      }
  </script>

  <script>
    $(document).ready(function(){
      getLocations();
      locationSet = '<?php echo $location_set;?>';
      typeSet = '<?php echo $type_set;?>';
      errorSet = '<?php echo $error_set;?>';

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
      
    });
  </script>
  <script>
    //this doesn't always trigger, but sometimes the above code screws up and this is needed
    window.onload = function(){
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

    $(document).ready(function(){
      $(".input-daterange").datepicker({
        orientation: "bottom",
        format: "MM/dd/yyyy",
        startDate: "today",
        todayHighlight: true
      });
      /*$("#start-date-input").datepicker({
        orientation: "bottom",
        format: "MM/dd/yyyy",
        todayHighlight: true
      });
      $("#end-date-input").datepicker({
        orientation: "bottom",
        format: "MM/dd/yyyy",
        todayHighlight: true
      });*/
      //this makes sure that they know when they can try and submit the things, validation is after this(in case they are feeling cheeky)
      $("#beds-other-textarea").keyup( function(){
        if ($(this).val().length > 0){
          $("#beds-btn-submit").show();
        }else{
          $("#beds-btn-submit").hide();
        }
      });

      $("#baths-other-textarea").keyup( function(){
        if ($(this).val().length > 0){
          $("#baths-btn-submit").show();
        }else{
          $("#baths-btn-submit").hide();
        }
      });
        
      $("#travel-frequency-other-textarea").keyup( function(){
        if ($(this).val().length > 0){
          $("#travel-frequency-btn-submit").show();
        }else{
          $("#travel-frequency-btn-submit").hide();
        }
      });

      //temporary; for testing
      $("#dates-btn-submit").show();
      //

      //for dates we check if not null for the button display, however for the validation then the js comes into play
      $("#start-date-input, #end-date-input").change( function(){
        if (($("#start-date-input").val().length > 0) && ($("#end-date-input").val().length > 0)){
          $("#dates-btn-submit").show();
        }else{
          $("#dates-btn-submit").hide();
        }
      });


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

        //Show types container
        getTypesContainer();
        if (typeSet == false){//so that it doesn't double back if we already have type set
          jumpTo('types-container');
        }
      });
      
      $("#types-container").on("click", ".btn-option", function(){
        //unclick any other buttons in the types-container
        unclickButtons('types-container');
        buttonClicked($(this));
        if (typeSet == ''){//Don't want this if we already have it set in the url
          type = $(this).text();
        }

        $("#types-container").fadeTo("slow", 0.4);//fade type container
        
        if (type == "Furnished"){
          $("#baths-container").fadeOut(0.4);
          unclickButtons('baths-container');
          baths = "";
          $("#pets-container").fadeOut(0.4);
          unclickButtons('pets-container');
          pets = [];
          if ($("#dates-container").is(":visible")){//the one after baths/beds
            $("#travel-frequency-container").fadeTo("fast", 1);
          }
          if ($("#other-cities-container").is(":visible")){//the one after baths/beds
            $("#nights-visiting-container").fadeTo("fast", 1);
          }
        }else{
          $("#travel-frequency-container").fadeOut(0.4);
          unclickButtons('travel-frequency-container');
          travelFrequency = "";
          $("#nights-visiting-container").fadeOut(0.4);
          unclickButtons('nights-visiting-container');
          nightsVisiting = []; 
          if ($("#dates-container").is(":visible")){//the one after baths/beds
            $("#baths-container").fadeTo("fast", 1);
          }
          if ($("#other-cities-container").is(":visible")){//the one after baths/beds
            $("#pets-container").fadeTo("fast", 1);
          }
        }
        getPriorityContainer();
        jumpTo('priority-container');
      });

      $("#priority-container").on("click", ".btn-option", function(){
        unclickButtons('priority-container');
        buttonClicked($(this));
        priority = $(this).text();
        $("#priority-container").fadeTo("slow", 0.4);
        getBedsContainer();
        jumpTo('beds-container');
      });

      $("#beds-container").on("click", ".btn-option", function(){
        unclickButtons('beds-container');
        buttonClicked($(this));
        beds = $(this).text();
        $("#beds-container").fadeTo("slow", 0.4);
        getBathsContainer();
      });

      $("#beds-container").on("click", ".btn-other", function(){
        unclickButtons('beds-container');
        beds = "";
        $("#beds-buttons").fadeOut("fast", function(){
          $("#beds-other-textarea").fadeIn("fast");//200 ms
        });
      });

      $("#baths-container").on("click", ".btn-option", function(){
        unclickButtons('baths-container');
        buttonClicked($(this));
        baths = $(this).text();
        $("#baths-container").fadeTo("slow", 0.4);
        getTravelFrequencyContainer();
      });

      $("#baths-container").on("click", ".btn-other", function(){
        unclickButtons('baths-container');
        baths = "";
        $("#baths-buttons").fadeOut("fast", function(){
          $("#baths-other-textarea").fadeIn("fast");//200ms
        });
      });

      $("#travel-frequency-container").on("click", ".btn-option", function(){
        unclickButtons('travel-frequency-container');
        buttonClicked($(this));
        travelFrequency = $(this).text();

        $("#travel-frequency-container").fadeTo("slow", 0.4);
        getDatesContainer();
      });

      $("#travel-frequency-container").on("click", ".btn-other", function(){
        unclickButtons('travel-frequency-container');
        travelFrequency = "";
        $("#travel-frequency-buttons").fadeOut("fast", function(){
          $("#travel-frequency-other-textarea").fadeIn("fast");//200ms
        });
      });

      $("#nights-visiting-container").on("click", ".btn-option", function(){
        buttonClicked($(this));
        //only add if clicked on
        if ($(this).attr('class').indexOf("btn-clicked") > -1){
          nightsVisiting.push($(this).text());
        }else{
          
          var index = nightsVisiting.indexOf($(this).text());
          if (index > -1){
            nightsVisiting.splice(index, 1);
          }
        }
        nightsVisitingButtonsClicked = document.getElementById('nights-visiting-container').getElementsByClassName('btn-clicked');
        if (nightsVisitingButtonsClicked.length > 0){
          $("#nights-visiting-btn-continue").fadeIn(0.1);
        }else{
          $("#nights-visiting-btn-continue").fadeOut(0.1);
        }
      });
      
      $("#nights-visiting-container").on("click", ".btn-all", function(){
        //buttonClicked($(this));//don't highlight the all button
        if (nightsVisiting.length < 7){
          clickButtons("nights-visiting-container");
          nightsVisiting = [];//save us the trouble of avoiding dupes
          nightsVisitingButtons = document.getElementById("nights-visiting-container").getElementsByClassName("btn-option");
          for (var nightsVisitingButton in nightsVisitingButtons){
            if (nightsVisitingButtons[nightsVisitingButton].innerText != "All"){
              nightsVisiting.push(nightsVisitingButtons[nightsVisitingButton].innerText);
            }else{
              break;
            }
          }
        }else{//they are already all clicked, unclick all
          unclickButtons("nights-visiting-container");
          nightsVisiting = [];
        }
        if (nightsVisiting.length > 0){
          $("#nights-visiting-btn-continue").fadeIn(0.1);
        }else{
          $("#nights-visiting-btn-continue").fadeOut(0.1);
        }
      });

      $("#pets-container").on("click", ".btn-option", function(){
        buttonClicked($(this));
        //only add if clicked on
        if ($(this).attr('class').indexOf("btn-clicked") > -1){
          pets.push($(this).text());
        }else{
          var index = nightsVisiting.indexOf($(this).text());
          if (index > -1){
            pets.splice(index, 1);
          }
        }
      });

      $("#pets-container").on("click", ".btn-other", function(){
        unclickButtons('pets-container');
        baths = "";
        $("#pets-buttons").fadeOut("fast", function(){
          $(this).replaceWith($("#pets-other-textarea").hide());
          $("#pets-other-textarea").fadeIn("fast");//200ms
          $("#pets-buttons").fadeIn("fast");//200ms
        });
        $("#pets-btn-continue").fadeIn("normal");//400ms
      });

      $("#other-cities-container").on("click", ".btn-option", function(){
        buttonClicked($(this));
        //only add if clicked on
        if ($(this).attr('class').indexOf("btn-clicked") > -1){
          otherCities.push($(this).text());
        }else{
          var index = otherCities.indexOf($(this).text());
          if (index > -1){
            otherCities.splice(index, 1);
          }
        }
      });
      
      $("#other-cities-container").on("click", ".btn-all", function(){
        //buttonClicked(button);//don't highlight the all button
        if (otherCities.length < locations[state].length-1){//don't count the already selected on
          clickButtons("other-cities-container");
          otherCities = [];//save us the trouble of avoiding dupes
          otherCitiesButtons = document.getElementById("other-cities-container").getElementsByClassName("btn-option");
          for (var otherCitiesButton in otherCitiesButtons){
            if (otherCitiesButtons[otherCitiesButton].innerText != "All"){
              otherCities.push(otherCitiesButtons[otherCitiesButton].innerText);
            }else{
              break;
            }
          }
        }else{//they are already all clicked, unclick all
          unclickButtons("other-cities-container");
          otherCities = [];
        }
      });

      $("#home-types-container").on("click", ".btn-option", function(){
        buttonClicked($(this));
        //only add if clicked on
        if ($(this).attr('class').indexOf("btn-clicked") > -1){
          homeTypes.push($(this).text());
        }else{
          var index = homeTypes.indexOf($(this).text());
          if (index > -1){
            homeTypes.splice(index, 1);
          }
        }
      });
      
      $("#home-types-container").on("click", ".btn-all", function(){
        //buttonClicked(button);//don't highlight the all button
        if (homeTypes.length < 5){
          clickButtons("home-types-container");
          homeTypes = [];//save us the trouble of avoiding dupes
          homeTypesButtons = document.getElementById("home-types-container").getElementsByClassName("btn-option");
          for (var homeTypesButton in homeTypesButtons){
            if (homeTypesButtons[homeTypesButton].innerText != "All"){
              homeTypes.push(homeTypesButtons[homeTypesButton].innerText);
            }else{
              break;
            }
          }
        }else{//they are already all clicked, unclick all
          unclickButtons("home-types-container");
          homeTypes = [];
        }
      });

      $("#nights-visiting-container").on("click", ".btn-continue", function(){
        $("#nights-visiting-container").fadeTo("slow", 0.4);

        getOtherCitiesContainer();
      });

      $("#pets-container").on("click", ".btn-continue", function(){
        $("#pets-container").fadeTo("slow", 0.4);

        //since the pets continue btn will be visible all the time, regardless of if they clicked other or not, we need to get whatever
        //is in the textarea only if the textarea is visible.

        if ($("#pets-other-textarea").is(":visible")){
          pets = document.getElementById("pets-other-textarea").value;
        }
        getOtherCitiesContainer();
      });

      $("#other-cities-container").on("click", ".btn-continue", function(){
        $("#other-cities-container").fadeTo("slow", 0.4);

        getHomeTypesContainer();
        jumpTo('home-types-container');
      });

      $("#home-types-container").on("click", ".btn-continue", function(){
        $("#home-types-container").fadeTo("slow", 0.4);

        getParkingNeedsContainer();
        jumpTo('parking-needs-container');
      });

      $("#parking-needs-container").on("click", ".btn-continue", function(){
        parkingNeeds = document.getElementById("parking-needs-textarea").value;
        $("#parking-needs-container").fadeTo("slow", 0.4);

        getCommentsContainer();
        jumpTo('comments-container');
      });

      $("#comments-container").on("click", ".btn-continue", function(){
        comments = document.getElementById("comments-textarea").value;
        $("#comments-container").fadeTo("slow", 0.4);

        getContactInfoContainer();
        jumpTo('contact-info-container');
      });
      //Begin Form validation
      /*
      $("#beds-other-form").submit( function (event){
        event.preventDefault();
        $("#beds-container").fadeTo("slow", 0.4);

        beds = document.getElementById("beds-other-textarea").value;
        $("#beds-container").fadeTo("slow", 0.4);
        getBathsContainer();
      });

      $("#baths-other-form").submit( function (event){
        event.preventDefault();
        $("#baths-container").fadeTo("slow", 0.4);

        baths = document.getElementById("baths-other-textarea").value;
        $("#baths-container").fadeTo("slow", 0.4);
        getTravelFrequencyContainer();
      });

      $("#dates-form").submit( function (event){
        //this makes sure that there are values input
        event.preventDefault();

        startDate = $("#start-date-input").val();
        endDate = $("#end-date-input").val();
        var currentDate = new Date();

        if (Date.parse(startDate) < currentDate){
          alert("Your travel start date must be today or later.");
        }else if(Date.parse(startDate) > Date.parse(endDate)){
          alert("Your travel end date must be greater than or equal to your travel start date.");
        }else{
          $("#dates-container").fadeTo("slow", 0.4);

          getNightsVisitingContainer();
        }
      });

      $("#contact-info-form").submit( function (event){
        event.preventDefault();

        firstName = document.getElementById("fname-input").value;
        lastName = document.getElementById("lname-input").value;
        phone = document.getElementById("phone-input").value;
        email = document.getElementById("email-input").value;
        $("#contact-info-container").fadeTo("slow", 0.4);

        getBudgetContainer();
        jumpTo('budget-container');
      });

      //need to have this make sure the max is greater than min price
      $("#budget-form").submit( function (event){
        //event.preventDefault();//uncomment this when we want to avoid the actual submitting
        minPrice = $("#min-price-input").val();
        maxPrice = $("#max-price-input").val();
        if (minPrice > maxPrice){
          alert("Your minimum price must be greater than your maximum price.");
        }else{
          budget = document.getElementById("max-price-input").value;
          $("#budget-container").fadeTo("slow", 0.4);
          //$("#whitespace").fadeOut();//might be better to keep this out 
        }
      });
      */
      //End Form Validation

      //the following is just to save time while testing.
      //I also removed the required tags for dates, contact info, and budget
      $("#beds-other-form").submit( function (event){
        event.preventDefault();
        $("#beds-container").fadeTo("slow", 0.4);

        beds = document.getElementById("beds-other-textarea").value;
        $("#beds-container").fadeTo("slow", 0.4);
        getBathsContainer();
      });

      $("#baths-other-form").submit( function (event){
        event.preventDefault();
        $("#baths-container").fadeTo("slow", 0.4);

        baths = document.getElementById("baths-other-textarea").value;
        $("#baths-container").fadeTo("slow", 0.4);
        getTravelFrequencyContainer();
      });

      $("#travel-frequency-other-form").submit( function (event){
        event.preventDefault();
        $("#travel-frequency-container").fadeTo("slow", 0.4);

        travelFrequency = document.getElementById("travel-frequency-other-textarea").value;

        $("#travel-frequency-container").fadeTo("slow", 0.4);
        getDatesContainer();
      });

      $("#dates-form").submit( function (event){
        //this makes sure that there are values input
        event.preventDefault();

        startDate = $("#start-date-input").val();
        endDate = $("#end-date-input").val();

        $("#dates-container").fadeTo("slow", 0.4);

        getNightsVisitingContainer();
      });

      $("#contact-info-form").submit( function (event){
        event.preventDefault();

        firstName = document.getElementById("fname-input").value;
        lastName = document.getElementById("lname-input").value;
        phone = document.getElementById("phone-input").value;
        email = document.getElementById("email-input").value;
        $("#contact-info-container").fadeTo("slow", 0.4);

        getBudgetContainer();
        jumpTo('budget-container');
      });

      //need to have this make sure the max is greater than min price
      $("#budget-form").submit( function (event){
        event.preventDefault();//we need to keep this so that we can actually post all the data we want
        minPrice = $("#min-price-input").val();
        maxPrice = $("#max-price-input").val();
        budget = maxPrice;
        $("#budget-container").fadeTo("slow", 0.4);

        post("/tulip/new_site_design/form_handler.php", {state: state, city: city, type: type, priority: priority, beds: beds, baths: baths, travelFrequency: travelFrequency, startDate: startDate, endDate: endDate, nightsVisiting: nightsVisiting, pets: pets, otherCities: otherCities, homeTypes: homeTypes, parkingNeeds: parkingNeeds, comments: comments, firstName: firstName, lastName: lastName, phone: phone, email: email, budget: budget});
        //$("#whitespace").fadeOut();//might be better to keep this out 
      });
      //and it's gone.

     /*
      $("#fname-input").keyup( function(){
        if (($("#fname-input").val().length > 0) && ($("#lname-input").val().length > 0)){
          $("#contact-info-btn-continue").show();
        }else if($("#fname-input").val().length <= 0){
          $("#contact-info-btn-continue").hide();
        }
      });

      $("#min-price-input").keyup( function(){
        if (($("#min-price-input").val().length > 0) && ($("#max-price-input").val().length >0)){
          //if they are both not empty
          $("#btn-submit").show();
        }else if(($("#min-price-input").val().length <= 0)){
          //if either is empty
          $("#btn-submit").hide();
        }
      });

      $("#max-price-input").keyup( function(){
        if (($("#max-price-input").val().length > 0) && ($("#min-price-input").val().length >0)){
          //if they are both not empty
          $("#btn-submit").show();
        }else if(($("#max-price-input").val().length <= 0)){
          //if either is empty
          $("#btn-submit").hide();
        }
      });
      */

    });

    //[0-9 \(\).\-]+     
    /*if ((is_numeric(phone1) && is_numeric(phone2) && is_numeric(phone3)) == false){
      alert("Your phone number must be a number.")
      return false;
    }
      
    if (start_date && end_date){
      if (Date.parse(start_date) > current_date){
        if (Date.parse(start_date) >= Date.parse(end_date)){
          alert("Your travel end date must be greater than your travel start date.");
          return false;
        }
      }else{
        alert("Your travel start date must be today or later.");
        return false;
      }
    }
     */

  </script>
</head>
<body>
  <!--Location Selection-->
  <div align="center" class="container">
    
    <div id="wrapper">
      <div id="location-container">
        <span class="page-header-subtitle">
          Where are you looking for a home?<p class='form-required'>*</p>
        </span>
        <span id="current-location-text">
          <i class="fa fa-map-marker"></i> Current Location: Not Set
        </span>

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
      <div class="question-container" id="types-container">
        <span class='page-header-subtitle'>What type of home are you looking for?<p class='form-required'>*</p></span>
        <button type='button' align='center' class='btn-option'>Unfurnished</button>
        <button type='button' align='center' class='btn-option'>Furnished</button>
      </div>
      <!--End Types-->
      
      <!--Priority-->
      <div class="question-container" id="priority-container" title="This will help us find the perfect home for you!">
        <span class='page-header-subtitle'>What is more important to you?<p class='form-required'>*</p></span>
        <button type='button' align='center' class='btn-option'>Staying within Budget</button>
        <button type='button' align='center' class='btn-option'></button>
        <button type='button' align='center' class='btn-option'>Neither</button>
      </div>
      <!--End Priority-->
      
      <!--Beds-->
      <div class="question-container" id="beds-container">
        <span class='page-header-subtitle'>How many Bedrooms do you need?<p class='form-required'>*</p></span>
        <form id="beds-other-form">
          <textarea class="other-textarea" id="beds-other-textarea" cols="45" rows="7" required></textarea>
          <br><button type='submit' align='center' id='beds-btn-submit' class='btn-continue'>Continue</button>
        </form>
        <div id="beds-buttons">
          <div class="row">
            <button type='button' align='center' class='btn-option'>1</button>
            <button type='button' align='center' class='btn-option'>2</button>
            <button type='button' align='center' class='btn-option'>3</button>
          </div>
          <div class="row">
            <button type='button' align='center' class='btn-option'>4</button>
            <button type='button' align='center' class='btn-option'>5</button>
            <button type='button' align='center' class='btn-other'>Other</button>
          </div>
        </div>
      </div>
      <!--End Beds-->

      <!--Baths-->
      <div class="question-container" id="baths-container">
        <span class='page-header-subtitle'>How many Bathrooms do you need?<p class='form-required'>*</p></span>
        <form id="baths-other-form">
          <textarea class="other-textarea" id="baths-other-textarea" cols="45" rows="7" required></textarea>
          <br><button type='submit' align='center' id='baths-btn-submit' class='btn-continue'>Continue</button>
        </form>
        <div id="baths-buttons">
          <div class="row">
            <button type='button' align='center' class='btn-option'>1</button>
            <button type='button' align='center' class='btn-option'>2</button>
            <button type='button' align='center' class='btn-option'>3</button>
          </div>
          <div class="row">
            <button type='button' align='center' class='btn-option'>4</button>
            <button type='button' align='center' class='btn-option'>5</button>
            <button type='button' align='center' class='btn-other'>Other</button>
          </div>
        </div>
      </div>
      <!--End Baths-->

      <!--Travel Frequency-->
      <div class="question-container" id="travel-frequency-container">
        <span class='page-header-subtitle'></span>
        <form id="travel-frequency-other-form">
          <textarea class="other-textarea" id="travel-frequency-other-textarea" cols="45" rows="7" required></textarea>
          <br><button type='submit' align='center' id='travel-frequency-btn-submit' class='btn-continue'>Continue</button>
        </form>
        <div id="travel-frequency-buttons">
          <button type='button' align='center' class='btn-option'>Weekly</button>
          <button type='button' align='center' class='btn-option'>Every Other Week</button>
          <button type='button' align='center' class='btn-option'>Once a Month</button>
          <button type='button' align='center' class='btn-option'>Just Once</button>
          <button type='button' align='center' class='btn-other'>Irregular Travel</button>
        </div>
      </div>
      <!--End Travel Frequency-->

      <!--Start & End Dates-->
      <div class="question-container form-container" id="dates-container">
        <span class="page-header-subtitle">When does your contract start and end?<p class="form-required">*</p></span>
        <form id="dates-form">
          <div class="row">
            <!--<div class="col-md-6">
              <input type='text' align='center' id='start-date-input' class='form-control'>
            </div>
            <div class="col-md-6">
              <input type='text' align='center' id='end-date-input' class='form-control'>
            </div>-->
            <div class="input-daterange input-group" id="datepicker">
              <input value="Travel Start Date" type="text" id="start-date-input" class="input-sm form-control" name="start" />
              <span class="input-group-addon">to</span>
              <input value="Travel End Date" type="text" id="end-date-input" class="input-sm form-control" name="end" />
            </div>
          </div>
          <br><button type='submit' align='center' id='dates-btn-submit' class='btn-continue'>Continue</button>
        </form>
      </div>
      <!--End Start & End Dates-->

      <!--Nights Visiting-->
      <div class="question-container" id="nights-visiting-container">
        <span class='page-header-subtitle'></span>
        <div class="row">
          <button type='button' align='center' class='btn-option'>Sunday</button>
          <button type='button' align='center' class='btn-option'>Monday</button>
          <button type='button' align='center' class='btn-option'>Tuesday</button>
          <button type='button' align='center' class='btn-option'>Wednesday</button>
        </div>
        <div class="row">
          <button type='button' align='center' class='btn-option'>Thursday</button>
          <button type='button' align='center' class='btn-option'>Friday</button>
          <button type='button' align='center' class='btn-option'>Saturday</button>
          <button type='button' align='center' class='btn-option btn-all'>All</button>
        </div>
        <button type='button' align='center' id='nights-visiting-btn-continue' class='btn-continue'>Continue</button>
      </div>
      <!--End Nights Visiting-->

      <!--Pets-->
      <div class="question-container" id="pets-container">
        <span class='page-header-subtitle'>Pets?</span>
        <textarea class="other-textarea" id="pets-other-textarea" cols="45" rows="7"></textarea>
        <div id="pets-buttons">
          <button type='button' align='center' class='btn-option'>Dog(s)</button>
          <button type='button' align='center' class='btn-option'>Cat(s)</button>
          <button type='button' align='center' class='btn-option btn-other'>Other</button>
        </div>
        <br><button type='button' align='center' id='pets-btn-continue' class='btn-continue'>Continue</button>
      </div>
      <!--End Pets-->

      <!--Other Cities-->
      <div class="question-container" id="other-cities-container">
      </div>
      <!--End Other Cities-->

      <!--Home Types-->
      <div class="question-container" id="home-types-container">
        <span class='page-header-subtitle'>Select acceptable home types: </span>
        <div class="row">
          <button type='button' align='center' class='btn-option'>Single Family Home</button>
          <button type='button' align='center' class='btn-option'>Duplex</button>
          <button type='button' align='center' class='btn-option'>Apartment</button>
        </div>
        <div class="row">
          <button type='button' align='center' class='btn-option'>Condo</button>
          <button type='button' align='center' class='btn-option'>Townhouse</button>
          <button type='button' align='center' class='btn-option btn-all'>All</button>
        </div>
        <button type='button' align='center' class='btn-continue'>Continue</button>
      </div>
      <!--End Home Types-->

      <!--Parking Needs-->
      <div class="question-container" id="parking-needs-container">
        <span class='page-header-subtitle'>Parking Needs: </span>
        <textarea id="parking-needs-textarea" cols='50' rows='5'></textarea><br>
        <button type='button' align='center' class='btn-continue'>Continue</button>
      </div>
      <!--End Parking Needs-->

      <!--Comments-->
      <div class="question-container" id="comments-container">
        <span class='page-header-subtitle'>Comments and Questions: </span>
        <textarea id="comments-textarea" cols='50' rows='10'></textarea><br>
        <button type='button' align='center' class='btn-continue'>Continue</button>
      </div>
      <!--End Comments-->

      <!--Contact Info-->
      <div align="center" class="question-container form-container" id="contact-info-container">
        <span class='page-header-subtitle'>What information should we contact you with?<p class="form-required">*</p></span>
        <form id="contact-info-form">
          <div class='row'>
            <div class='col-md-6'>
              <input type='text' align='center' class='form-control' name='fname' id='fname-input' placeholder='First Name'>
            </div>
            <div class='col-md-6'>
              <input type='text' align='center' class='form-control' name='lname' id='lname-input' placeholder='Last Name'>
            </div>
          </div>
          <div class='row'>
            <div class='col-md-6'>
              <input type='phone' align='center' class='form-control' name='phone' id='phone-input' placeholder='Phone #'>
            </div>
            <div class='col-md-6'>
              <input type='email' align='center' class='form-control' name='email' id='email-input' placeholder='Email'>
            </div>
          </div>
          <br><button type='submit' align='center' id='contact-info-btn-continue' class='btn-continue' >Continue</button>

        </form>
      </div>
      <!--End Contact Info-->

      <!--Budget-->
      <div class="question-container form-container" id="budget-container">
        <span class="page-header-subtitle">What's your housing budget?<p class="form-required">*</p></span>
        <form id="budget-form">
          <div class="row">
            <div class="col-md-6">
              <input placeholder="$ Min Price"  type='number' step="0.01" align='center' id="min-price-input" class='form-control'>
            </div>
            <div class="col-md-6">
              <input placeholder="$ Max Price" type='number' step="0.01" align='center' id="max-price-input" class='form-control'>
            </div>
          </div>
          <br><button type='submit' align='center' id="btn-budget-submit" class='btn-continue'>Submit</button>
        </div>
      </form>
      <!--End Budget-->

      <div id="whitespace">
      </div>
    </div>
  </div>
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.js"></script>
</body>
</html>
