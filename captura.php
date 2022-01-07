<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Supernatural</title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

$(document).ready(function() {

	var mapCenter = new google.maps.LatLng(-23.5550445,-46.6775025); //Google map Coordinates
	var map;
	
	map_initialize(); // initialize google map
	
	//############### Google Map Initialize ##############
	function map_initialize()
	{
			var googleMapOptions = 
			{ 
				center: mapCenter, // map center
				zoom: 17, //zoom level, 0 = earth view to higher value
				maxZoom: 20,
				minZoom: 10,
				zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL //zoom control size
			},
				scaleControl: true, // enable scale control
				mapTypeId: google.maps.MapTypeId.ROADMAP // google map type
			};
		
		   	map = new google.maps.Map(document.getElementById("google_map"), googleMapOptions);			
			
			//Load Markers from the XML File, Check (map_process.php)
			$.get("map_process.php", function (data) {
				$(data).find("markers").each(function () {
					  var nome 		= $(this).attr('nome');
					  var poder 		= $(this).attr('poder');
					  var defesa 		= $(this).attr('defesa');
					  var habilidade 	= '<p>'+ $(this).attr('habilidade') +'</p>';
					  var type 		= $(this).attr('type');
					  var point 	= new google.maps.LatLng(parseFloat($(this).attr('lat')),parseFloat($(this).attr('lng')));
					  create_marker(point, nome,poder,defesa, habilidade, false, false, false, type);
				});
			});	
			
		
			//Right Click to Drop a New Marker
			google.maps.event.addListener(map, 'rightclick', function(event) {
				//Edit form to be displayed with new marker
				var EditForm = '<p><div class="marker-edit">'+
				'<form action="ajax-save.php" method="POST" name="SaveMarker" id="SaveMarker">'+
				'<label for="pNome"><span>Nome :</span><input type="text" name="pNome" class="save-nome" placeholder="Nome" maxlength="15" /></label>'+
				'<label for="pPoder"><span>Poder :</span><input type="number" name="pPoder" class="save-poder" placeholder="Poder" maxlength="15" /></label>'+
				'<label for="pDefesa"><span>Defesa :</span><input type="number" name="pDefesa" class="save-defesa" placeholder="Defesa" maxlength="15" /></label>'+
				'<label for="pHabilidade"><span>Habilidade :</span>'+
				'<select name="pHabilidade" class="save-habilidade">'+
				'<option value="Fogo">Fogo</option>'+
				'<option value="Agua">Agua</option>'+
				'<option value="Regigiao">Religiao</option>'+
				'<option value="Frio">Frio</option>'+
				'</select></label>'+
				
				'<label for="pType"><span>Tipo :</span>'+
				'<select name="pType" class="save-type">'+
				'<option value="icons/maria.gif">Maria</option>'+
				'<option value="icons/odio.gif">Odio</option>'+
				'<option value="icons/religiao.gif">Religiao</option>'+
				'<option value="icons/tonto.gif">Tonto</option>'+
				'</select></label>'+
				'</form>'+
				'</div></p><button name="save-marker" class="save-marker">Adicionar Ponto</button>';

				//Drop a new Marker with our Edit Form
				create_marker(event.latLng, 'Novo ', EditForm, true,true,true, "icons/pin.png");
			});
										
	}
	
	//############### Create Marker Function ##############
	function create_marker(MapPos, MapNome,MapPoder,MapDefesa,MapHabilidade,  InfoOpenDefault, DragAble, Removable, iconPath)
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
		'<h1 class="marker-heading">'+MapNome+'&nbsp;&nbsp;</h1>Habilidade: '+MapHabilidade+ '&nbsp;&nbsp;<br> Poder: '+MapPoder+'<br> Defesa: '+MapDefesa+
		'<br><hr>'+
		'</span><br><button  name="remove-marker" class="remove-marker" title="Eliminar Monstro" ><img src="icons/roub.png"></button>'+
		'</div></div>');	

		
		//Create an infoWindow
		var infowindow = new google.maps.InfoWindow();
		//set the content of infoWindow
		infowindow.setContent(contentString[0]);

		//Find remove button in infoWindow
		var removeBtn 	= contentString.find('button.remove-marker')[0];
		var saveBtn 	= contentString.find('button.save-marker')[0];

		//add click listner to remove marker button
		google.maps.event.addDomListener(removeBtn, "click", function(event) {
			remove_marker(marker);
		});
		
		if(typeof saveBtn !== 'undefined') //continue only when save button is present
		{
			//add click listner to save marker button
			google.maps.event.addDomListener(saveBtn, "click", function(event) {
				var mReplace = contentString.find('span.info-content'); //html to be replaced after success
				var mNome = contentString.find('input.save-nome')[0].value; //name input field value
				var mPoder = contentString.find('input.save-poder')[0].value; //name input field value
				var mDefesa = contentString.find('input.save-defesa')[0].value; //name input field value
				var mHabilidade  = contentString.find('select.save-habilidade')[0].value; //description input field value
				var mType = contentString.find('select.save-type')[0].value; //type of marker
				
				if(mNome =='' || mHabilidade ==''|| mPoder =='')
				{
					alert("Falta a Um Dado Importante!");
				}else{
					save_marker(marker, mNome,mPoder,mDefesa, mHabilidade, mType, mReplace); //call save marker function
				}
			});
		}
		
		//add click listner to save marker button		 
		google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker); // click on marker opens info window 
	    });
		  
		if(InfoOpenDefault) //whether info window should be open by default
		{
		  infowindow.open(map,marker);
		  
		}
	}
	
	//############### Remove Marker Function ##############
	function remove_marker(Marker)
	{
		
		/* determine whether marker is draggable 
		new markers are draggable and saved markers are fixed */
		if(Marker.getDraggable()) 
		{
			Marker.setMap(null); //just remove new marker
		}
		else
		{
			//Remove saved marker from DB and map using jQuery Ajax
			var mLatLang = Marker.getPosition().toUrlValue(); //get marker position
			var myData = {del : 'true', latlang : mLatLang}; //post variables
			$.ajax({
			  type: "POST",
			  url: "map_process.php",
			  data: myData,
			  success:function(data){
					Marker.setMap(null); 
					alert(data);
				},
				error:function (xhr, ajaxOptions, thrownError){
					alert(thrownError); //throw any errors
				}
			});
		}

	}
	
	//############### Save Marker Function ##############
	function save_marker(Marker, mNome,mPoder,mDefesa, mHabilidade, mType, replaceWin)
	{
		//Save new marker using jQuery Ajax
		var mLatLang = Marker.getPosition().toUrlValue(); //get marker position
		var myData = {nome : mNome,poder : mPoder,defesa : mDefesa, habilidade : mHabilidade, latlang : mLatLang, type : mType }; //post variables
		console.log(replaceWin);		
		$.ajax({
		  type: "POST",
		  url: "map_process.php",
		  data: myData,
		  success:function(data){
				replaceWin.html(data); //replace info window with new html
				Marker.setDraggable(false); //set marker to fixed
				Marker.setIcon('icons/pin.png'); //replace icon
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(thrownError); //throw any errors
            }
		});
	}

});
</script>

<style type="text/css">
h1.heading{padding:0px;margin: 0px 0px 10px 0px;text-align:center;font: 18px Georgia, "Times New Roman", Times, serif;}

/* width and height of google map */
#google_map {width: 90%; height: 500px;margin-top:0px;margin-left:auto;margin-right:auto;}

/* Marker Edit form */
.marker-edit label{display:block;margin-bottom: 5px;}
.marker-edit label span {width: 100px;float: left;}
.marker-edit label input, .marker-edit label select{height: 24px;}
.marker-edit label textarea{height: 60px;}
.marker-edit label input, .marker-edit label select, .marker-edit label textarea {width: 60%;margin:0px;padding-left: 5px;border: 1px solid #DDD;border-radius: 3px;}

/* Marker Info Window */
h1.marker-heading{color: #585858;margin: 0px;padding: 0px;font: 18px "Trebuchet MS", Arial;border-bottom: 1px dotted #D8D8D8;}
div.marker-info-win {margin-right: 20px;}
div.marker-info-win p{padding: 0px;margin: 10px 0px 10px 0;}
div.marker-inner-win{padding: 5px;}
button.save-marker, button.remove-marker{border: none;background: rgba(0, 0, 0, 0);color: #00F;padding: 0px;text-decoration: underline;margin-right: 10px;cursor: pointer;
}
</style>
</head>
<body>             
<h1 class="heading">Supernatural</h1>
<div align="center">click com o Botao Direito do Mouser Para Adicionar o Monstro</div><br>
<div id="google_map"></div>
</body>
</html>