jQuery(document).ready(function(){
	
	jQuery("#address_country").change(function(){
		uai_sp_crv_country_change();
	});
	var st = jQuery("#address_state").val();
	
	if(st===null) uai_sp_crv_country_change();

	jQuery("#address_state").change(function(){
		uai_sp_crv_state_change();
	});
});

function uai_sp_crv_country_change(){
	var ctry = jQuery("#address_country").val();
	jQuery("#address_state").html("");
	jQuery("#address_state").attr("disabled", "disabled");
	jQuery("#address_city").html("");
	jQuery("#address_city").attr("disabled", "disabled");
	if(!isNaN(ctry)){
	  jQuery.post("/",{endpoint: "sp_geographic_get_states",country_code:ctry}, function(data, status){
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

function uai_sp_crv_state_change(){
	var state = jQuery("#address_state").val();	
	jQuery("#address_city").html("");
	jQuery("#address_city").attr("disabled", "disabled");
	if(!isNaN(state)){
	  jQuery.post("/",{endpoint: "sp_geographic_get_cities",state_code:state}, function(data, status){
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