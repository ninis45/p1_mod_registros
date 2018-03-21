$(document).ready(function(){
    
    display_autocomplete = display_autocomplete? display_autocomplete:'<div><strong>{{participante}}</strong> <br/> {{module_id}}</div>';
    
    //var reg_open = new RegExp(display_autocomplete, '\{');
    //var reg_close = new RegExp(display_autocomplete, '}');
   
    display_autocomplete= display_autocomplete.replace(/{/g,'{{');
    display_autocomplete = display_autocomplete.replace(/}/g,'}}');
    
    console.log(display_autocomplete);
    var input_text = $('.typeahead');
    var list_autocomplete= new Bloodhound({
      //datumTokenizer: Bloodhound.tokenizers.whitespace,
      //queryTokenizer: Bloodhound.tokenizers.whitespace,
      // `states` is an array of state names defined in "The Basics"
       datumTokenizer: Bloodhound.tokenizers.obj.whitespace('participante'),
       queryTokenizer: Bloodhound.tokenizers.whitespace,
      
       local: data?data:[]
    });
    
    $('#form').on('submit',function(){
        
        var opts = {
            position:'absolute',
            top:'20%'
           
        };
        var spinner = new Spinner(opts).spin();
         $(this).prepend(spinner.el);
         
         $(this).find('button[type="submit"]').attr('disabled',true);
         
        
    });
    
    $('#text_auto').typeahead(null,/*{
      hint: true,
      highlight: true,
      minLength: 1
    },*/
    {
      name: 'list_autocomplete',
      display: 'participante',
      source: list_autocomplete,
      templates: {
          empty: [
            '<div class="empty-message alert">',
              text_empty,
            '</div>'
          ].join('\n'),
          suggestion: Handlebars.compile(display_autocomplete)
        }
    });
    
    input_text.bind('typeahead:select',function(e,suggestion){
        $('button[type="submit"]').attr('disabled',true);
        $('input[name="module_id"]').val(suggestion.module_id);
        $('input[name="participante"]').val(suggestion.participante);
        
        $('input[name="id"]').val(suggestion.id);
        
        $('#form').submit();
        ///location.href=url_current+'?participante='+suggestion.participante+'&module_id='+suggestion.module_id;
        
    });
    
    
    if($('#map_front').length>0)
    {
       
    
        evento.init(lat,lng,zoom);
        
        evento.set_marker(evento.position,evento.map);
    }
    
   
});

var evento={
    map:false,
    position:false,
    marker:false,
    dragable:false,
    options:{
        
        
    },
    init:function(lat,lng,zoom)
    {
        var styles = [
          {
            stylers: [
              { hue: "#002b06" },
              { saturation: -70 }
            ]
          },{
            featureType: "road",
            elementType: "geometry",
            stylers: [
              { lightness: -10 },
              { visibility: "simplified" }
            ]
          },{
            featureType: "road",
            elementType: "labels",
            stylers: [
              { visibility: "off" }
            ]
          }
        ];
        evento.position={lat: lat, lng: lng};
        
        evento.options={
            zoom:zoom,
            center: evento.position,
    	    mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        
        evento.map = new google.maps.Map(document.getElementById('map_front'), evento.options);
        //centro.map.setOptions({styles: styles});
        
        
    },
    set_marker:function(position,map,url_icon)
    {
        
       
        
        var infowindow = new google.maps.InfoWindow();
        
        evento.marker = new google.maps.Marker({
			position: position,
			map: map,
			//title: data.title,
            draggable:evento.dragable,
            icon: url_icon,
            
            
               
			
        });
        
    }
}