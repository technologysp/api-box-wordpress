
jQuery(document).ready(function(){

	jQuery("#address_country").change(function(){
		sp_crv_country_change();
	});
	var st = jQuery("#address_state").val();
	
	if(st===null) sp_crv_country_change();

	jQuery("#address_state").change(function(){
		sp_crv_state_change();
	});

	if(jQuery("#sp_apibox_calculation_result").length ){
		 jQuery('html, body').animate({  scrollTop: jQuery("#sp_apibox_calculation_result").offset().top-20}, 1000);
	}

});

function sp_crv_country_change(){
	var ctry = jQuery("#address_country").val();
	var destination=window.location.href;
	jQuery("#address_state").html("");
	jQuery("#address_state").attr("disabled", "disabled");
	jQuery("#address_city").html("");
	jQuery("#address_city").attr("disabled", "disabled");	
	if(!isNaN(ctry)){
	  jQuery.post(destination,{endpoint: "sp_geographic_get_states",country_code:ctry}, function(data, status){
	    	if(data){	    
	    		
	    		for(var state in data){
	    			var o = new Option(data[state], state);
	    			jQuery(o).html(data[state]);
					jQuery("#address_state").append(o);
	    		}
	    	}
	  }, "json").always(function( data ) {
				    jQuery("#address_state").removeAttr("disabled");
				    jQuery("#address_city").removeAttr("disabled");
				  });
	}
}

function sp_crv_state_change(){
	var state = jQuery("#address_state").val();	
	var destination=window.location.href;
	jQuery("#address_city").html("");
	jQuery("#address_city").attr("disabled", "disabled");
	if(!isNaN(state)){
	  jQuery.post(destination,{endpoint: "sp_geographic_get_cities",state_code:state}, function(data, status){
	    	if(data){	    		
	    		for(var state in data){
	    			var o = new Option(data[state], state);
	    			jQuery(o).html(data[state]);
					jQuery("#address_city").append(o);
	    		}
	    	}
	  }, "json").always(function( data ) {				    
				    jQuery("#address_city").removeAttr("disabled");
				  });
	}
}