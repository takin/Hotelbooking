function QuickView(data) {
    this.data = data;
}

QuickView.gmap = null;
QuickView.pweb_filter = null;
QuickView.pweb_map = null;
QuickView.propertyList = [];
QuickView.currentProperty = null;
QuickView.propertyNumberToIndex = {};

QuickView.remove = function(propertyNumber) {
    var index = QuickView.propertyNumberToIndex[propertyNumber.toString()];

    if (index == undefined) {
        return;
    }

    QuickView.propertyNumberToIndex = {};

    // remove it
    QuickView.propertyList.splice(index, 1);

    for (var i = 0; i < QuickView.propertyList.length; i++) {
        QuickView.propertyNumberToIndex[ QuickView.propertyList[i].data.propertyNumber ] = i;
    }
};

QuickView.addProperty = function(data) {
    var currentIndex = QuickView.propertyList.length;

    QuickView.propertyList.push(new QuickView(
            {
                propertyNumber: data.propertyNumber,
                amenities: data.amenities,
                propertyTypeTranslate: data.propertyTypeTranslate,
                propertyName: data.propertyName,
                address1: data.address1,
                city_name: data.city_name,
                property_page_url: data.property_page_url,
                districts: data.districts,
                landmarks: data.landmarks,
                Geo: data.Geo,
                BIGIMAGES: data.propertyInfo ? data.propertyInfo.BIGIMAGES : [],
                HW_IMAGES: data.propertyInfo ? data.propertyInfo.PropertyImages : [],
                IMPORTANTINFORMATION: data.propertyInfo ? data.propertyInfo.IMPORTANTINFORMATION : '',
                conditionsTranslated: data.propertyInfo ? data.propertyInfo.conditionsTranslated : '',
                conditions: data.propertyInfo ? data.propertyInfo.conditions : '',
                isHW: data.hasOwnProperty('overallHWRating') ? true : false
            }
    ));

    //QuickView.propertyNumberToIndex[data.propertyNumber.toString()] = currentIndex;
};

QuickView.moveToIndex = function(index) {
    if (index == undefined || typeof(QuickView.propertyList[index]) == 'undefined') {
        return undefined;
    }

    var obj = QuickView.propertyList[index];

    $('#quick_preview_div').empty().html(obj.getContent());

    obj.setMap();
};

QuickView.getObject = function(propertyNumber) {
    var index = QuickView.propertyNumberToIndex[propertyNumber];

    if (index == undefined) {
        return undefined;
    }

    return QuickView.propertyList[index];
};

QuickView.prototype.getContent = function() {
    var wait_message = $('#wait_message').val();

    if (this.data.isHW) {
        var text = '<div class="loading-dispo-city loading-quick-preview" id="loading-pics"><p>' + wait_message + '</p></div>';
        $('#quick_preview_div').empty().append(text);
    } else {
        var text = '<div class="loading-dispo-city loading-quick-preview" id="loading-pics"><p>' + wait_message + '</p></div>';
        $('#quick_preview_div').empty().append(text);
    }


    QuickView.currentPropertyIndex = QuickView.propertyNumberToIndex[ this.data.propertyNumber.toString() ];

    var nextid = $('#prop_tab_box_' + this.data.propertyNumber.toString()).next().attr('rel');
    var preid = $('#prop_tab_box_' + this.data.propertyNumber.toString()).prev().attr('rel');

    var includes = [];
    var amenities = [];

    for (var i = 0; i < this.data.amenities.length; i++) {
        if (this.data.amenities[i]['type'] == 'feature') {
            amenities.push(this.data.amenities[i]);
        }
        else {
            includes.push(this.data.amenities[i]);
        }
    }

    var propertyTypeTranslate = this.data.propertyTypeTranslate ? ' (' + this.data.propertyTypeTranslate + ')' : '';

    var self = this;

    var images = [];

    if (self.data.isHW) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            cache: true,
            url: 'http://' + window.location.host + '/cmain/ajax_property_details/' + this.data.propertyNumber,
            success: function(data) {
                self.data.Geo = {
                    Latitude: data.hostel.geolatitude,
                    Longitude: data.hostel.geolongitude
                };
                var imageList = data.hostel.BIGIMAGES ? data.hostel.BIGIMAGES : data.hostel['PropertyImages'];

                for (var i = 0; i < imageList.length; i++) {
                    if (imageList[i].imageType == 'Main') {
                        images.push(imageList[i]);
                    }
                }

                // now load the info
                var content = Mustache.to_html(document.getElementById('template-property-quick-view').innerHTML, {
                    propertyNmae: self.data.propertyName + propertyTypeTranslate,
                    address1: self.data.address1,
                    city_name: self.data.city_name,
                    propertyUrl: self.data.property_page_url,
                    Latitude: self.data.Geo.Latitude,
                    Longitude: self.data.Geo.Longitude,
                    IMAGES: [],
                    HW_IMAGES: images,
                    hasIncludes: includes.length,
                    hasAmenities: amenities.length,
                    includes: includes,
                    amenities: amenities,
                    allAmenities: self.data.amenities,
                    hasDistricts: self.data.districts.length ? true : false,
                    noDistricts: self.data.districts.length ? false : true,
                    districts: self.data.districts,
                    hasLandmarks: self.data.landmarks.length ? true : false,
                    noLandmarks: self.data.landmarks.length ? false : true,
                    landmarks: self.data.landmarks,
                    propertyHasImportantInfo: data.hostel.IMPORTANTINFORMATION || data.hostel.conditions ? true : false,
                    propertyConditionsTranslated: data.hostel.conditionsTranslated ? true : false,
                    propertyConditionsTranslatedText: data.hostel.conditionsTranslated,
                    propertyConditionsOriginal: data.hostel.conditions,
                    hasPropertyConditions: data.hostel.conditions && !data.hostel.conditionsTranslated ? true : false,
                    propertyConditions: data.hostel.conditions,
                    hasPropertyInfo: data.hostel.IMPORTANTINFORMATION ? true : false,
                    propertyInfo: data.hostel.IMPORTANTINFORMATION,
                    prevIndex: preid ? QuickView.propertyNumberToIndex[preid] : undefined,
                    nextIndex: nextid ? QuickView.propertyNumberToIndex[nextid] : undefined,
                    isHB: self.data.isHW ? false : true
                });

                $('#map_canvas').remove();

                $('#quick_preview_div').empty().html(content);

                // remove the not found images
                var imageList = [];
                for (var i = 0; i < images.length; i++) {
                    imageList[i] = new Image();

                    imageList[i].onerror = function() {
                        var current = $(this);
                        $('img[src="' + current.attr('src') + '"]').parent().parent().remove();
                    };

                    imageList[i].onabort = function() {
                        var current = $(this);
                        $('img[src="' + current.attr('src') + '"]').parent().parent().remove();
                    };

                    imageList[i].src = images[i];
                }

                var imagesNo = parseInt($('.ad-thumb-list li').length, 10);

                $('.ad-gallery').adGallery({
                    start_at_index: 5,
                    loader_image: '/images/loading-round.gif',
                    width: 400,
                    height: 300
                });

                // seems like Mustache encodes HTML entities
                if (data.hostel.IMPORTANTINFORMATION) {
                    $('#bottomfeature1 .bottom-feature-data1 .group').html(data.hostel.IMPORTANTINFORMATION);
                }
                else {
                    if (data.hostel.conditionsTranslated) {
                        $('#bottomfeature1 .bottom-feature-data1 .group .translated').html(data.hostel.conditionsTranslated);
                        $('#bottomfeature1 .bottom-feature-data1 .group .original').html(data.hostel.conditions);
                    }
                }

                $('#showmore').toggle(
                        function() {
                            $("#bottomfeature1").fadeIn("slow");
                            $('.fancybox-inner').scrollTop(900);

                            $('#showmore .showmore_plus_sign').hide();
                            $('#showmore .showmore_minus_sign').show();

                        },
                        function() {
                            $("#bottomfeature1").fadeOut("slow");

                            $('#showmore .showmore_plus_sign').show();
                            $('#showmore .showmore_minus_sign').hide();
                        }
                );


                // set first items selected
                $('#hostel_mapView_districts input[type="radio"]').eq(0).attr('checked', true);
                //$('#hostel_mapView_landmarks input[type="radio"]').eq(0).attr('checked', true);


                self.setMap();

                var freeHtml = $('.free').html();
                $(freeHtml).prependTo('#quick_preview_div .icon_facility_extra3');
            }
        });
    } else {

        $.ajax({
            type: 'GET',
            dataType: 'json',
            cache: true,
            url: 'http://' + window.location.host + '/cmain/ajax_property_details/' + this.data.propertyNumber,
            success: function(data) {

                self.data.Geo = {
                    Latitude: data.hostel.GPS.LAT,
                    Longitude: data.hostel.GPS.LON
                };

                var imageList = data.hostel.BIGIMAGES ? data.hostel.BIGIMAGES : data.hostel.IMAGES;

                for (var i = 0; i < imageList.length; i++) {
                    images.push(imageList[i]);
                }

                // now load the info
                var content = Mustache.to_html(document.getElementById('template-property-quick-view').innerHTML, {
                    propertyNmae: self.data.propertyName + propertyTypeTranslate,
                    address1: self.data.address1,
                    city_name: self.data.city_name,
                    propertyUrl: self.data.property_page_url,
                    Latitude: self.data.Geo.Latitude,
                    Longitude: self.data.Geo.Longitude,
                    IMAGES: images,
                    HW_IMAGES: [],
                    hasIncludes: includes.length,
                    hasAmenities: amenities.length,
                    includes: includes,
                    amenities: amenities,
                    allAmenities: self.data.amenities,
                    hasDistricts: self.data.districts.length ? true : false,
                    noDistricts: self.data.districts.length ? false : true,
                    districts: self.data.districts,
                    hasLandmarks: self.data.landmarks.length ? true : false,
                    noLandmarks: self.data.landmarks.length ? false : true,
                    landmarks: self.data.landmarks,
                    propertyHasImportantInfo: data.hostel.IMPORTANTINFORMATION || data.hostel.conditions ? true : false,
                    propertyConditionsTranslated: data.hostel.conditionsTranslated ? true : false,
                    propertyConditionsTranslatedText: data.hostel.conditionsTranslated,
                    propertyConditionsOriginal: data.hostel.conditions,
                    hasPropertyConditions: data.hostel.conditions && !data.hostel.conditionsTranslated ? true : false,
                    propertyConditions: data.hostel.conditions,
                    hasPropertyInfo: data.hostel.IMPORTANTINFORMATION ? true : false,
                    propertyInfo: data.hostel.IMPORTANTINFORMATION,
                    prevIndex: preid ? QuickView.propertyNumberToIndex[preid] : undefined,
                    nextIndex: nextid ? QuickView.propertyNumberToIndex[nextid] : undefined,
                    isHB: self.data.isHW ? false : true
                });

                $('#map_canvas').remove();

                $('#quick_preview_div').empty().html(content);

                // remove the not found images
                var imageList = [];
                for (var i = 0; i < images.length; i++) {
                    imageList[i] = new Image();

                    imageList[i].onerror = function() {
                        var current = $(this);
                        $('img[src="' + current.attr('src') + '"]').parent().parent().remove();
                    };

                    imageList[i].onabort = function() {
                        var current = $(this);
                        $('img[src="' + current.attr('src') + '"]').parent().parent().remove();
                    };

                    imageList[i].src = images[i];

                }

                var imagesNo = parseInt($('.ad-thumb-list li').length, 10);

                $('.ad-gallery').adGallery({
                    start_at_index: 5,
                    loader_image: '/images/loading-round.gif',
                    width: 400,
                    height: 300
                });

                // seems like Mustache encodes HTML entities
                if (data.hostel.IMPORTANTINFORMATION) {
                    $('#bottomfeature1 .bottom-feature-data1 .group').html(data.hostel.IMPORTANTINFORMATION);
                }
                else {
                    if (data.hostel.conditionsTranslated) {
                        $('#bottomfeature1 .bottom-feature-data1 .group .translated').html(data.hostel.conditionsTranslated);
                        $('#bottomfeature1 .bottom-feature-data1 .group .original').html(data.hostel.conditions);
                    }
                }

                $('#showmore').toggle(
                        function() {
                            $("#bottomfeature1").fadeIn("slow");
                            $('.fancybox-inner').scrollTop(900);

                            $('#showmore .showmore_plus_sign').hide();
                            $('#showmore .showmore_minus_sign').show();

                        },
                        function() {
                            $("#bottomfeature1").fadeOut("slow");

                            $('#showmore .showmore_plus_sign').show();
                            $('#showmore .showmore_minus_sign').hide();
                        }
                );


                // set first items selected
                $('#hostel_mapView_districts input[type="radio"]').eq(0).attr('checked', true);
                //$('#hostel_mapView_landmarks input[type="radio"]').eq(0).attr('checked', true);


                self.setMap();

                var freeHtml = $('.free').html();
                $(freeHtml).prependTo('#quick_preview_div .icon_facility_extra3');
            }
        });

    }
};

QuickView.prototype.setMap = function() {
    $('#map_canvas').css('height', '285px !important');
    var that = this;

    var lat = this.data.Geo.Latitude;
    var lng = this.data.Geo.Longitude;

    pweb_filter.addFilterMap('hostel_quickview', "map_canvas", 'en', lat, lng);
    pweb_filter.toggleMap('city');
    pweb_filter.toggleMap('hostel_quickview');


    function autoselect() {

        if ($('input[name="distrinct_landmark"]:checked').length > 0) {
            try {
                pweb_filter.changeDistrictLayer("hostel_quickview", $('input[name="distrinct_landmark"]:checked').val());
            } catch (err) {
            }
        }

        
    }

    window.setTimeout(function() {
        autoselect();
    }, 2200);
};

/**
 * Helper for QuickView
 */
function QuickViewHelper() {
}

QuickViewHelper.prototype.init = function() {
    this.bind();
};

QuickViewHelper.prototype.bind = function() {
    $('a.show-room-info').click(function() {
        return false;
    });

    $('a.show-room-info').mouseover(function() {
        $(this).next().show();
    });

    $('a.show-room-info').mouseleave(function() {
        $(this).next().hide();
    });

    $('#showmore').toggle(
            function() {
                $('#showmore .showmore_plus_sign').hide();
                $('#showmore .showmore_minus_sign').show();

                $("#bottomfeature1").fadeIn("slow");
            },
            function() {
                $('#showmore .showmore_plus_sign').show();
                $('#showmore .showmore_minus_sign').hide();

                $("#bottomfeature1").fadeOut("slow");
            }
    );

    $('#showmorereviews').toggle(
            function() {
                $("#bottomfeature2").fadeIn("slow");
            },
            function() {
                $("#bottomfeature2").fadeOut("slow");
            }
    );

    var self = this;

    $('#switch-effect').change(
            function() {
                self.galleries[0].settings.effect = $(this).val();

                return false;
            }
    );

    $('#toggle-slideshow').click(
            function() {
                self.galleries[0].slideshow.toggle();

                return false;
            }
    );

    $('#toggle-description').click(
            function() {
                if (!self.galleries[0].settings.description_wrapper) {
                    self.galleries[0].settings.description_wrapper = $('#descriptions');
                }
                else {
                    self.galleries[0].settings.description_wrapper = false;
                }

                return false;
            }
    );
};

QuickViewHelper.prototype.showImage = function(imageurl) {
    document.getElementById("largeimage").src = imageurl;
};

$(document).ready(function() {
    var quickViewHelper = new QuickViewHelper();

    quickViewHelper.init();
});


function showimage(imageurl) {
    document.getElementById("largeimage").src = imageurl;
}
