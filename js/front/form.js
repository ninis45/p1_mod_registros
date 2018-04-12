$(document).ready(function(){
    
    if(display_autocomplete){
        display_autocomplete = display_autocomplete? display_autocomplete:'<div><strong>{{participante}}</strong> <br/> {{module_id}}</div>';
        
        
       
        display_autocomplete= display_autocomplete.replace(/{/g,'{{');
        display_autocomplete = display_autocomplete.replace(/}/g,'}}');
        
       
        var input_text = $('.typeahead');
        var list_autocomplete= new Bloodhound({
          
           datumTokenizer: Bloodhound.tokenizers.obj.whitespace('participante'),
           queryTokenizer: Bloodhound.tokenizers.whitespace,
          
           local: data?data:[]
        });
    }
    $('.input-textarea').on('blur',function(){
        var value = $(this).val();
        
        
        
        
        $('#container-chips').append('<span class="item">'+value+' <a href="javascript:;" onclick="this.parentElement.style.display=\'none\'" ><i class="fa fa-close"></i></a></span>');
        $(this).val('');
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
    if(display_autocomplete){
        $('#text_auto').typeahead(null,
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
           
            
        });
    
    }
    if($('#map_front').length>0)
    {
       
    
        evento.init(lat,lng,zoom);
        
        evento.set_marker(evento.position,evento.map);
    }
    var rama_selectize = $('select[name="rama"]').selectize(),
            rama = $('#rama').find('select'),
            div  = $('#rama'),
            disciplina = $('#disciplina').val(),
            selectize  = rama_selectize[0].selectize;
    
    
    
     $.each(disciplinas,function(index,value){
           
            
            if(value.id == disciplina){
                if(value.rama != '0')
                {
                    div.removeClass('hide');
                    
                }
                switch(value.rama)
                {
                        
                        case '1':
                           // html += '<option value="1">Varonil</option>';
                            selectize.addOption({value:'1',text:'Varonil'});
                        break;
                        case '2':
                            //html+='<option value="2">Femenil</option>';
                            selectize.addOption({value:'2',text:'Femenil'});
                        break;
                        case '3':
                            //html += '<option value="1">Varonil</option><option value="2">Femenil</option>';
                            
                            selectize.addOption({value:'1',text:'Varonil'});
                            selectize.addOption({value:'2',text:'Femenil'});
                        break;
                }
                return false;
            }
            
     });
     
     selectize.setValue(value_rama);
     //console.log(selectize);
    $('select[name="disciplina"]').on('change',function(){
        
        var html = '<option value="">Elegir</option>';
        
        disciplina = $(this).val();
        
         selectize.clearOptions();
        $.each(disciplinas,function(index,value){
           
            if(value.id == disciplina)
            {
                
                if(value.rama == '0')
                {
                    div.addClass('hide');
                }
                else
                    div.removeClass('hide');
                switch(value.rama)
                {
                    
                    case '1':
                       // html += '<option value="1">Varonil</option>';
                        selectize.addOption({value:'1',text:'Varonil'});
                    break;
                    case '2':
                        //html+='<option value="2">Femenil</option>';
                        selectize.addOption({value:'2',text:'Femenil'});
                    break;
                    case '3':
                        //html += '<option value="1">Varonil</option><option value="2">Femenil</option>';
                        
                        selectize.addOption({value:'1',text:'Varonil'});
                        selectize.addOption({value:'2',text:'Femenil'});
                    break;
                }
                
                
                rama.html(html);
                return false;
            }
            
        });
    });
   // $('#disciplina').trigger('change');
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