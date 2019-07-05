jQuery(document).ready(function(){

	jQuery( "#date_of_birth" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:"dd/mm/yy"
    });

	jQuery("#address_country").change(function(){
		sp_crv_country_change();
	});
	var st = jQuery("#address_state").val();
		
	if(st===null) sp_crv_country_change();

	jQuery("#address_state").change(function(){
		sp_crv_state_change();
	});
});

function sup_validate_req_field(field){
	switch(jQuery(field).attr("type")){
		case 'email':
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  			if(jQuery(field).val()=="" || !emailReg.test( jQuery(field).val() )){
  				jQuery(field).addClass("is-invalid");
				return false;
  			}
			break;

		default:
			if(jQuery(field).val()==""){
				jQuery(field).addClass("is-invalid");
				return false;
			}
			break;
	}	
	return true;
}

function sup_v_all_elements_validate(){

}

var currentstep=1;//always start 1
function sup_next(element){
	jQuery(".form-result-messages").fadeOut();
	jQuery("#form_section"+currentstep).find("input,textarea,select").each(function(){
		jQuery(this).removeClass("is-invalid")
	})
	if (currentstep<3){

		var do_continue=true;
		jQuery("#form_section"+currentstep).find("input,textarea,select").each(function(){
			console.log(jQuery(this).attr("name"));
			if(jQuery(this).hasClass('required')){
				if(!sup_validate_req_field(this)) do_continue=false;
			}
		}).promise().done( function(){
			if(do_continue){
				
				jQuery("#form_section"+currentstep).fadeOut(500, function(){
					currentstep+=1;
					jQuery("#form_section"+currentstep).fadeIn(500);	
				});
			}
		} );		
	}
}

function sup_prev(element){
	if (currentstep>1){
		jQuery("#form_section"+currentstep).fadeOut(500, function(){
			currentstep-=1;
			jQuery("#form_section"+currentstep).fadeIn(500);	
		});
		
	}
}

function sp_crv_country_change(){
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

function sp_crv_state_change(){
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