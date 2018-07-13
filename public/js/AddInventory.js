$(document).ready(function() 
{

$("#category").on('change', function(){   
    $(".ajax").css('visibility','hidden');
    var v = $('#category option:selected').val();
    
    $.ajax({
        url: "{{ path_for('inventory.ajax') }}", 
        data: {
            id: v
        },
        type: "POST",
        dataType : "json",
    })

    .done(function( result ) {
    
        $.each(result,function(key, value){  
            prop = value.property.property;                                       
            $("#attrib_"+(key+1)).css('visibility','visible');
            $("#attrib_"+(key+1)+" label").text(prop);
            var Att = 'attrib_'+(key+1);

            if(value.property.dataType == "String"){                               
               $("#input_"+(key+1)).html(
                    '<input type="text" id="'+Att+'" name="'+Att+'" class="form-control">'
                );

            } else if(value.property.dataType == "Number"){                              
               $("#input_"+(key+1)).html(
                    '<input type="number" id="'+Att+'" name="'+Att+'" class="form-control">'
                ); 

            } else if(value.property.dataType == "Boolean"){                              
               $("#input_"+(key+1)).html(
                    '<select name="'+Att+'" id="'+Att+'" class="form-control"> <option value="0">No</option><option value="1">Yes</option></select>'
                );                                
            }                           
        });
    })

    .fail(function( xhr, status, errorThrown ) {
    alert(  "Error: " + errorThrown );
    console.log( "Error: " + errorThrown );
    console.log( "Status: " + status );
    console.dir( xhr );
  })

  // Code to run regardless of success or failure;
  .always(function( xhr, status ) {
    
    }); 
  });    
});