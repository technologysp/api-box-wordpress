var spapibox_invoice_elementid='trck_nmr_fol_invoice';
jQuery(document).ready(function(){
	
	/*jQuery( "#start_date" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:"yy-mm-dd"
    });

    jQuery( "#end_date" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:"yy-mm-dd"
    });*/   

    jQuery('#A'+spapibox_invoice_elementid).on("change", function(){ spapibox_invoice_showname(null); });
    let elefile = document.getElementById(spapibox_invoice_elementid); 

    jQuery("#button_trck_nmr_fol_invoice").click(function(){
      document.getElementById(spapibox_invoice_elementid).click();
      jQuery(".file_selected_name").html('');
      return false;
    });

    elefile.onclick = function () {      
      this.value = null;      
    };
    elefile.onchange = function () {      
      var name = document.getElementById(spapibox_invoice_elementid); 
      jQuery(".file_selected_name").html(name.files.item(0).name);
    };
});


function spapibox_invoice_showname(ele) {
      var name = document.getElementById(spapibox_invoice_elementid);       
};

