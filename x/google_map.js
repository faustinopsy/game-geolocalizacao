$(document).ready(function() {

	var mapCenter = new google.maps.LatLng(-14.1368671699895, -441621484374999); //Google map Coordinates
	var map;
	
	map_initialize(); // initialize google map
	
	//############### Google Map Initialize ##############
	function map_initialize()
	{
			var googleMapOptions = 
			{ 
				center: mapCenter, // map center
				zoom: 5, //zoom level, 0 = earth view to higher value
				maxZoom: 20,
				minZoom: 5,
				zoomControlOptions: { style: google.maps.ZoomControlStyle.SMALL //zoom control size
				
				
			},
				scaleControl: true, // enable scale control
				mapTypeId: google.maps.MapTypeId.ROADMAP // google map type
			};
		
		   	map = new google.maps.Map(document.getElementById("google_map"), googleMapOptions);			
			
			//Load Markers from the XML File, Check (map_process.php)
			$.get("x/map_sp.php", function (data) {
				$(data).find("sp").each(function () {
					  var name 		= $(this).attr('name');
					   var data 		= $(this).attr('data');
					    var hora 		= $(this).attr('hora');
						 var bo 		= $(this).attr('bo');
					  var address 	= '<p>'+ $(this).attr('address') +'</p>';
					   var uf 		= $(this).attr('uf');
					    var cidade 		= $(this).attr('cidade');
					  var type 		= $(this).attr('type');
					  var point 	= new google.maps.LatLng(parseFloat($(this).attr('lat')),parseFloat($(this).attr('lng')));
					  create_marker(point, name,address,data,hora,bo, uf, cidade, false, false, false,type);
				});
			});	
			
									
	}
	
	//############### Create Marker Function ##############
	function create_marker(MapPos, MapTitle,MapDesc,MapData,MapHora,MapBo,MapUf,MapCidade, InfoOpenDefault, DragAble, Removable, iconPath)
	{	  	  		  
		
		//new marker
		var marker = new google.maps.Marker({
			position: MapPos,
			map: map,
			draggable:DragAble,
			animation: google.maps.Animation.DROP,
			title:"Ponto X!",
			icon: iconPath
		});
		
		//Content structure of info Window for the Markers
		var contentString = $('<div class="marker-info-win">'+
		'<div class="marker-inner-win"><span class="info-content">'+
		'<h1 class="marker-heading">'+MapTitle+'</h1>'
		+ ' '+MapDesc+ '<br>'
		+ ' '+MapData+ ' <br> '
		+ ''+MapHora+ ' '+
		
		'</span>'+
		'</div></div>');	

		
		//Create an infoWindow
		var infowindow = new google.maps.InfoWindow();
		//set the content of infoWindow
		infowindow.setContent(contentString[0]);

		//Find remove button in infoWindow
		//var removeBtn 	= contentString.find('button.remove-marker')[0];
		var saveBtn 	= contentString.find('button.save-marker')[0];

		//add click listner to save marker button		 
		google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker); // click on marker opens info window 
				   var markerclusterer = new MarkerClusterer(infowindow);
	    });
		  
		if(InfoOpenDefault) //whether info window should be open by default
		{
		  infowindow.open(map,marker);
		  var markerclusterer = new MarkerClusterer(infowindow);
		}
	}
	
	

});