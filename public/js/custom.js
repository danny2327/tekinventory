$(document).ready(function() 
{
/*	$("#ddtest").on('change', function(){	
		var v = $('#ddtest option:selected').val();
		//alert( v );
        $.ajax({
        	url: "{{ path_for('inventory.ajax') }}", 
        	data: {
        		id: v
        	},
        	type: "POST",
        	dataType : "json",
		})

        .done(function( json ) {
        	alert( 'json' );
        	//success: function(result){
        	//$(".ajax").text(result);
    	})

        .fail(function( xhr, status, errorThrown ) {
	    alert( "Sorry, there was a problem!" );
	    console.log( "Error: " + errorThrown );
	    console.log( "Status: " + status );
	    console.dir( xhr );
	  })

	  // Code to run regardless of success or failure;
	  .always(function( xhr, status ) {
	    //alert( "The request is complete!" );
  		});   
	}); 
*/
$('[data-submenu]').submenupicker();


$("#test").click(function(){
	alert( {{ path_for('inventory.ajax') }} );

	$.ajax({
        	url: "{{ path_for('inventory.ajax') }}",         	
        	type: "post",
            data: {
                'name' : $("#name").val(),
                'rank' : $("#rank").val()
            },
        	dataType : "json",
		})

        .done(function( result ) {
        	//alert( result );
        	//success: function(result){
        	//$("#test").text(JSON.stringify(result));
        	$("#test").text("Hi " + result.rank + " " + result.name);
/*        	$.each(result,function(val){ 
        		$(".test").append(val) 
        	})*/
    	})

        .fail(function( xhr, status, errorThrown ) {
	    alert( errorThrown );
	    console.dir( xhr );
	  })

	  // Code to run regardless of success or failure;
	  .always(function( xhr, status ) {
	    ///alert(  );
  		});
      });
      });