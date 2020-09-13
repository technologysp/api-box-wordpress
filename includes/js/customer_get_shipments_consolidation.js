jQuery(document).ready(function(){
	
	jQuery( "#start_date" ).datepicker({
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
    });

});

function spapibox_continue_consolidation(){
  let awbs='';
  let pref='';
  let awbcount=0;  
  let awbprefix='<ul>';
  jQuery(".consolidation_shipments_selected").html('');
  jQuery(".checkable_item").each(function(){
        if(this.checked) {
          awbcount+=1;
          let selawb=jQuery(this).parent().attr('prop-awb');
          awbs+=pref + selawb;
          pref=',';

          awbprefix+='<li>'+selawb+'</li>';
        }

  });
  awbprefix+='</ul>';
  if(awbcount>0){
      jQuery(".consolidation_shipments_lists").fadeOut();
      jQuery(".consolidation_shipments_confirm").fadeIn();
  }
  jQuery("#trck_nmr_fol_list").val(awbs);
  jQuery(".consolidation_shipments_selected").html(awbprefix);
  console.log(awbs);
}

function apibox_conso_prev(){
    jQuery(".consolidation_shipments_lists").fadeIn();
    jQuery(".consolidation_shipments_confirm").fadeOut();
}
