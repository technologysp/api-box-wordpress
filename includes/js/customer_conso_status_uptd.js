jQuery(document).ready(function(){
	

    jQuery("#conso_toggled_box").change(function() {
      jQuery('#sp_customer_get_shipments_conso_status').click();//trigger('submit');
      //$("#second").click();
    });

	/*jQuery( "#start_date" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:"yy-mm-dd"
    });

    jQuery( "#end_date" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:"yy-mm-dd"
    });



    jQuery(".checkable_item_head").change(function() {
      if(this.checked) {

        jQuery(".checkable_item").each(function(){
            jQuery(this).attr("checked", true);
        });
        //Do stuff
      }else{
         jQuery(".checkable_item").each(function(){
            jQuery(this).attr("checked", false);
        });           
      }
      checkbox_changed_validate();
    });*/

});
