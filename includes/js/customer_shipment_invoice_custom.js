var spapibox_invoice_elementid='trck_nmr_fol_invoice';
jQuery(document).ready(function(){

    jQuery("#skpt_add_item_detail_validate").click(function(){

      
      var detgrand_total = 0;
      var errors = false;
      jQuery('.detail_display_row').each(function(){

        let desc = jQuery(this).find('.detdesc').val();
        if(desc==''){ jQuery(this).find('.detdesc').addClass('is-invalid'); errors=true; }else jQuery(this).find('.detdesc').removeClass('is-invalid');

        let qty = jQuery(this).find('.detqty').val().replace(/ /g,'');
        if(isNaN(qty) || qty==''){ jQuery(this).find('.detqty').addClass('is-invalid'); errors=true;}else jQuery(this).find('.detqty').removeClass('is-invalid');        

        let price = jQuery(this).find('.detprice').val().replace(/ /g,'');
        if(isNaN(price) || price==''){ jQuery(this).find('.detprice').addClass('is-invalid'); errors=true;}else jQuery(this).find('.detprice').removeClass('is-invalid');        

        if(!isNaN(qty) && !isNaN(price)){
          detgrand_total+=(qty*price);
        }


      }).promise().done( function(){ 

        if(!errors){
          jQuery("#skpt_add_item_detail_validate").fadeOut();
          jQuery("#sp_customer_invoice_uploader_custom").fadeIn();
          jQuery("#current_declared_value").html(detgrand_total);
          jQuery("#skpt_invoice_custom_totals").fadeIn();
          jQuery(".field_trck_add_deatil").fadeOut();
          jQuery("#sktp_inv_back_button").show();
        }

      });;

    });

    jQuery("#skpt_add_item_detail_ic").click(function(){
      skpt_current_detail_rows_idx+=1;

      jQuery("#sp_customer_invoice_uploader_custom").hide();
      jQuery("#skpt_add_item_detail_validate").show();
      jQuery(".field_trck_add_deatil").show();
      jQuery("#current_declared_value").html(0);
      jQuery("#skpt_invoice_custom_totals").hide();
      jQuery("#sktp_inv_back_button").hide();

      let idx=skpt_current_detail_rows_idx;
      let html='<tr id="skptinvdet-main_'+idx+'" class="detail_display_row"> <th class=""  style="min-width:200px;"> <input class="form-control  required detdesc" type="text" id="skptinvdet-desc_'+idx+'" name="skptinvdetdesc_'+idx+'" value=""></td>';
          html+='<td class=""><input class="form-control  required detqty" onchange="skpt_qty_price('+idx+')" type="text" id="skptinvdet-qty_'+idx+'" name="skptinvdet-qty_'+idx+'" value=""></td>';
          html+='<td class=""><input class="form-control  required detprice" onchange="skpt_chg_price('+idx+')" type="text" id="skptinvdet-price_'+idx+'" name="skptinvdet-price_'+idx+'" value=""></td>';
          html+='<td class=""><span id="skptinvdet-summary_'+idx+'">$ 0.00 </span></td>';
          html+='<td class=""><button id="skptinvdet-summary_'+idx+'" onclick="skpt_det_remove('+idx+')" type="button" class="btn btn-outline-danger btn-remove-inv-line">X</button>';
          html+='<input type="hidden" name = "skptinvdetidx_'+idx+'" value = "'+idx+'" /></td></tr>';
      
      jQuery("#skpt_tbody_details").append(html);

      sktp_inv_check_delete_buttons();
    });

    sktp_inv_check_delete_buttons();
});

function skpt_recalc_price(idx){

  jQuery("#sp_customer_invoice_uploader_custom").hide();
  jQuery("#skpt_add_item_detail_validate").show();
  jQuery(".field_trck_add_deatil").show();
  jQuery("#current_declared_value").html(0);
  jQuery("#skpt_invoice_custom_totals").hide();
  jQuery("#sktp_inv_back_button").hide();
  

  let qty = jQuery("#skptinvdet-qty_"+idx).val(); 
  let price = jQuery("#skptinvdet-price_"+idx).val();
  console.log('Q '+qty + ' P '+price);
  if(!isNaN(qty) && !isNaN(price)){
    let calc=qty*price;
    let val = parseFloat(calc);
    calc=val.toFixed(2);

    jQuery("#skptinvdet-summary_"+idx).html('$ '+calc);
  }else
    jQuery("#skptinvdet-summary_"+idx).html('');
}

function skpt_qty_price(idx){
  skpt_recalc_price(idx);
}

function skpt_chg_price(idx){
  skpt_recalc_price(idx);
}

function sktp_inv_check_delete_buttons(){
  if(jQuery('.detail_display_row').length <= 1){
    jQuery(".btn-remove-inv-line").hide();
  }else
    jQuery(".btn-remove-inv-line").show();
}

function skpt_det_remove(idx){
  if(jQuery('.detail_display_row').length > 1){
    jQuery("#skptinvdet-main_"+idx).remove();
    jQuery("#sp_customer_invoice_uploader_custom").hide();
    jQuery("#skpt_add_item_detail_validate").show();
    jQuery(".field_trck_add_deatil").show();
    jQuery("#current_declared_value").html(0);
    jQuery("#skpt_invoice_custom_totals").hide();
    jQuery("#sktp_inv_back_button").hide();
  }
  sktp_inv_check_delete_buttons();
}

function spapibox_invoice_showname(ele) {
      var name = document.getElementById(spapibox_invoice_elementid);       
};

