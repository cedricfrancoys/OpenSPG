(function( $ ) {

	function MapType(settings, map_el) {

		var settings = $.extend( {
			  'search_input_el'    : null,
			  'search_action_el'   : null,
			  'search_error_el'    : null,
			  'current_position_el': null,
			  'default_lat'        : '1',
			  'default_lng'        : '-1',
			  'default_zoom'       : 5,
			  'lat_field'          : null,
			  'lng_field'          : null,
			  'callback'           : function (location, gmap) {},
			  'error_callback'     : function(status) {
			  	$this.settings.search_error_el.text(status);
			  },
			}, settings);

		this.settings = settings;

		this.map_el = map_el;

		// this.geocoder = new google.maps.Geocoder();

	}

	MapType.prototype = {
		initMap : function(center) {

			// var center = new google.maps.LatLng(this.settings.default_lat, this.settings.default_lng);
			var center = {
				latlng: [36.892011,-3.245017]
			};
			var lat = this.settings.lat_field.val();
			var lon = this.settings.lng_field.val();
			if (lat && lon) {
				center = {
				latlng: [lat,lon]
			};
			}

			var mapOptions = {
				zoom: this.settings.default_zoom,
				center: center,
				mapTypeId: 'mapbox.mapbox-streets-v7',
				accessToken: 'pk.eyJ1IjoibWhhdXB0bWE3MyIsImEiOiJjajAxdm95cnUwMDhuMzNsdTEzcTlzYm55In0.PWU0PhdQ-GsakOmJyrXYPw'
			};
			
			var $this = this;
			
			// this.map =  new google.maps.Map(this.map_el[0], mapOptions);
			this.map = L.map(this.map_el[0]).setView([this.settings.default_lat,this.settings.default_lng], mapOptions.zoom);

			L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
				attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
				maxZoom: 18,
				id: mapOptions.mapTypeId,
				accessToken: mapOptions.accessToken
			}).addTo(this.map);

			this.setMarker(center);

			this.map.on('click', function(e){$this.setMarker(e);});

			// google.maps.event.addListener(this.marker, "dragend", function(event) {

			// 	var point = $this.marker.getPosition();
			// 	$this.map.panTo(point);
			// 	$this.updateLocation(point);

			// });

			// google.maps.event.addListener(this.map, 'click', function(event) {
			// 	$this.insertMarker(event.latLng);
			// });

			this.settings.search_action_el.click($.proxy(this.searchAddress, $this));
			
			this.settings.current_position_el.click($.proxy(this.currentPosition, $this));
		},

		searchAddress : function (e){
			e.preventDefault();
			var $this = this;
			var address = this.settings.search_input_el.val();
			// this.geocoder.geocode( { 'address': address}, function(results, status) {
			// 	if (status == google.maps.GeocoderStatus.OK) {
			// 		$this.map.setCenter(results[0].geometry.location);
			// 		$this.map.setZoom(16);
			// 		$this.insertMarker(results[0].geometry.location);
			// 	} else {
			// 		$this.settings.error_callback(status);
			// 	}
			// });
			var $this = this;
			$.get(
				'http://nominatim.openstreetmap.org/search?format=json&countrycodes=es&q=' + address,
				function(data){
					$.each(data, function(i,v){
						var marker = {
							latlng: [v.lat,v.lon],
							zoom: 12
						};
						$this.setMarker(marker);
					});
				}
			);
		},

		currentPosition : function(e){
			e.preventDefault();
			var $this = this;
			
			if ( navigator.geolocation ) {
				navigator.geolocation.getCurrentPosition ( 
					function(position) {
						var clientPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
						$this.insertMarker(clientPosition);
						$this.map.setCenter(clientPosition);
						$this.map.setZoom(16);
					}, 
					function(error) {
						$this.settings.error_callback(error);
					}
				);      
			} else {
				$this.settings.search_error_el.text('Your broswer does not support geolocation');
			}
			
		},

		updateLocation : function (location){
			this.settings.lat_field.val(location.latlng[0]);
			this.settings.lng_field.val(location.latlng[1]);
			this.settings.callback(location, this);
		},

		setMarker : function(marker) {
			var latlng = (marker.latlng) ? marker : marker.latlng;
			if (!(latlng.latlng instanceof Array)) {
				latlng = {
					latlng: [latlng.latlng.lat,latlng.latlng.lng]
				};
			}
			if( !this.marker ){
				this.marker = L.marker(latlng.latlng).addTo(this.map);
			}else{
				this.marker.setLatLng(latlng.latlng);
				if (latlng.zoom) {
					this.map.setZoom(latlng.zoom);
				}
				this.map.panTo(latlng.latlng);
				this.updateLocation(latlng);
			}
		},

		insertMarker : function (position) {
			this.removeMarker();

			this.addMarker(position);

			this.updateLocation(position);

		},
		removeMarker : function () {
			if(this.marker != undefined){
				this.marker.setMap(null);
			}
		}

	}

	$.fn.MapType = function(settings) {

		settings = $.extend({}, $.fn.MapType.defaultSettings, settings || {});

		return this.each(function() {
			var map_el = $(this);

			map_el.data('map', new MapType( settings, map_el ));

			map_el.data('map').initMap();

		});

	};
	
	$.fn.MapType.defaultSettings = {
			  'search_input_el'    : null,
			  'search_action_el'   : null,
			  'search_error_el'    : null,
			  'current_position_el': null,
			  'default_lat'        : '1',
			  'default_lng'        : '-1',
			  'default_zoom'       : 5,
			  'lat_field'          : null,
			  'lng_field'          : null,
			  'callback'           : function (location, gmap) {},
			  'error_callback'     : function(status) {
			  	$this.settings.search_error_el.text(status);
			  }
			}

	$(function(){
		$('#'+ogm.id+'_map_canvas').MapType({
		  'search_input_el'    : $('#'+ogm.id+'_input'),
		  'search_action_el'   : $('#'+ogm.id+'_search_button'),
		  'search_error_el'    : $('#'+ogm.id+'_error'),
		  'current_position_el': $('#'+ogm.id+'_current_position'),
		  'default_lat'        : ogm.default_lat,
		  'default_lng'        : ogm.default_lng,
		  'default_zoom'       : ogm.default_zoom,
		  'lat_field'          : $('#'+ogm.lat_field),
		  'lng_field'          : $('#'+ogm.lng_field),
		  'callback'           : maps_callback
		});
	});

})( jQuery );
