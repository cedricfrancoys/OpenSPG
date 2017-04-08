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

			this.map = L.map(this.map_el[0], {
				zoomControl: true, 
				center: center.latlng,
				zoom: this.settings.default_zoom,
				layers: [Spain_UnidadAdministrativa,Spain_PNOA_Ortoimagen]
			});

			var baselayers = {
				"PNOA Máx. Actualidad": Spain_PNOA_Ortoimagen,
				"Mapas IGN": Spain_MapasrasterIGN,
				"IGN Base": Spain_IGNBase,
				"MDT Elevaciones": Spain_MDT_Elevaciones,
				"Catastro": Spain_Catastro
			};

			var overlayers = {
				"Unidades administrativas": Spain_UnidadAdministrativa
			};

			L.control.layers(
				baselayers, 
				overlayers,
				{
					collapsed:false,
					zoom: this.settings.default_zoom
				})
			.addTo(this.map);

			this.setMarker(center);

			var $this = this;

			this.settings.search_action_el.click($.proxy(this.searchAddress, $this));
			
			this.settings.current_position_el.click($.proxy(this.currentPosition, $this));

			this.checkMarkerTitle();
		},

		checkMarkerTitle: function(){
			if (this.settings.markerTitleField && this.marker) {
				var frm = $(this.settings.search_input_el).parents('form').first();
				var frm_name = $(frm).attr('name');
				var titleFld = frm_name + '_' + this.settings.markerTitleField;
				var title = $('#'+titleFld).val();
				if( title != '' ){
					this.marker.bindTooltip(title).openTooltip();
				}
				if (typeof this.checkingTitleFld === 'undefined') {
					$('#'+titleFld).on('change', $.proxy(this.checkMarkerTitle, this));
					this.checkingTitleFld = true;
				}
			}
		},

		searchAddress : function (e){
			e.preventDefault();
			var $this = this;
			var address = this.settings.search_input_el.val();
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
						var latlng = {
							latlng: [position.coords.latitude, position.coords.longitude]
						}
						$this.setMarker(latlng);
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
			var $this = this;
			if( !this.marker ){
				this.marker = L.marker(
					latlng.latlng,
					{
						draggable: true
					}
				)
				.addTo(this.map)
				.on('dragend', function(e){
					var location = {
						latlng: [e.target._latlng.lat,e.target._latlng.lng]
					}
					$this.updateLocation(location);
				})
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
		  'callback'           : maps_callback,
		  'markerTitleField'   : ogm.markerTitleField
		});
	});

})( jQuery );
