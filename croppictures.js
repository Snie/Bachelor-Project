/* 
 * Author: Sonny Monti
 * A JS containing the functions needed to make the page cropPictures work
 */
var map;
var marker;
var last_collection;
var date_format = "yy/mm/dd";

function setupDatePicker(dateFrom,dateTo){
    var setup = function(){
        $("#pictures-results-date-from").datepicker({
            dateFormat: date_format,
            altField: "#pictures-results-internal-date-from"  ,
            altFormat: date_format
        });
        $("#pictures-results-date-from"  ).datepicker("setDate", dateFrom);
        $("#pictures-results-date-to"  ).datepicker({
            dateFormat: date_format,
            altField: "#pictures-results-internal-date-to"  ,
            altFormat: date_format,
            onSelect: function(){
                var newDate = $(this).datepicker('getDate');
                var fromDate = $("#pictures-results-date-from"  ).datepicker('getDate');
                if (newDate < fromDate) {
                    $(this).datepicker('setDate', fromDate);
                    $(this).blur();
                }
            },
            constrainInput: true
        });
        $("#pictures-results-date-to"  ).datepicker("setDate", dateTo);
        
        //append a function to the click event of the filter button
        $("#pictures-results-filter-button").click(function(){
            var dateFrom = $("#pictures-results-internal-date-from").val();
            var dateTo = $("#pictures-results-internal-date-to").val();
            var cropName = $("#crop-dropdown").val();

            $.ajax({
                url: 'cropPictures/loadCollections',
                type: "GET",
                data: {"cropName" : cropName, "dateFrom":dateFrom+"","dateTo":dateTo+""},
                dataType: "html",
                success: function(data){
                    $("#collection-dropdown").html(data);
                }
            });
        });
        //append a function to the click event of the infection button
        $("#pictures-results-infection-button").click(function(){
            $.ajax({
                url: 'cropPictures/infected',
                type: "GET",
                dataType: "html",
                success: function(data){
                    $("#pictures-list-content").html(data);
                        setupCarouselClick();
                        $("#pictures-list-content").trigger("updateSizes");
                        last_collection = undefined;
                        $("#compare-button").val("Compare plant over time");
                        
                }
            });
        });
    };
    try{
        setup();
    }catch(e){
        jQuery.noConflict();
        setup();
    }
};

var setupCarousel = function(){
    $("#pictures-list-content").carouFredSel({
        infinite: false,
        auto:false,
        items: {width:130,visible: { min: 1, max: 4 }, },
        prev: {
            button: "#foo2_prev",
  
        },
        next: {
            button: "#foo2_next",

        },
        pagination: "#foo2_pag"
    });
    
    $(".pagination").hide();
    
    setupCarouselClick();
    
    var myStyles =[
    {
        featureType: "poi",
        elementType: "labels",
        stylers: [
              { visibility: "off" }
        ]
    }
];
    var myOptions = {
      zoom: 19,
      zoomControl: true,
      mapTypeId: 'satellite',
      tilt:0,
      clickableIcons: false,
      styles:myStyles
    };
    map = new google.maps.Map(document.getElementById("picture-map"), myOptions);
    
    $("#compare-button").click(function(){
        if(last_collection == undefined){
            var picture_id = $('.picture-selected').find('#picture-id').val();
            $.ajax({
                url: 'cropPictures/comparePictures',
                type: "GET",
                data: {"picture_id" : picture_id},
                success: function(data){
                    if(data != "  "){
                        $("#compare-button").val("Return to collection");
                        $("#pictures-list-content").html(data);
                        setupCarouselClick();
                        $("#pictures-list-content").trigger("updateSizes");
                        last_collection = $("#collection-dropdown").val();
                    }
                    else{
                        $('.error').fadeIn(400).delay(3000).fadeOut(400);
                    }
                }
            });
        }
        else{
            var coll = last_collection.replace(" ", "_");
            coll = coll.replace(/:/g,"~")
            $.ajax({
            url: 'cropPictures/returnCollection',
            type: "GET",
            data: {"collection_date" :last_collection},
            dataType: "html",
            success: function(data){
                $("#compare-button").val("Compare plant over time");
                $("#pictures-list-content").html(data);
                setupCarouselClick();
                $("#pictures-list-content").trigger("updateSizes");
                last_collection= undefined;
            }
            });
        }
    });
}

var setupCarouselClick = function(){
    $(".pictures-collection").click(function(){
        $(this).addClass("picture-selected");
        $(this).siblings().removeClass("picture-selected");
        setupPictureInformation($(this).find('#picture-id').val());
    });
}

var setupPictureInformation = function(picture_id){
    $.ajax({
            url: 'cropPictures/pictureInformation',
            type: "GET",
            data: {"picture_id" : picture_id},
            success: function(data) {
                picture_data = jQuery.parseJSON(data);
                updateInformation(picture_data);
            }
        });
}

var updateInformation = function(data){
    $("#picture-preview-image").html(data.img_picture);
    $("#picture-date").html(data.date);
    $("#picture-latitude").html(data.latitude);
    $("#picture-longitude").html(data.longitude);
    $("#picture-user").html(data.user);
    $("#picture-device").html(data.device);
    $("#picture-hum").html(data.humidity);
    $("#picture-temp").html(data.temperature);
    $("#picture-rain").html(data.rain);
    $("#picture-lw").html(data.lw);
    $("#picture-crop").html(data.crop);
    
    var plant = new google.maps.LatLng(data.latitude,data.longitude);
    if(marker != undefined){
        marker.setMap(null);
    }
    marker = new google.maps.Marker({
          position: plant,
          map: map
        });
    map.setCenter(plant);
}