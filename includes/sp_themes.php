<?php
/**
 * Skypostal Themes handling
 *
 * @package Skypostal_apibox
 * @subpackage Themes
 * @since 1.0.0
 */

/**
 * Handles The available theming options to render FORM, FIELDS and GROUPS HTML.
 * 
 */
function spapibox_themes_themeForm($formContent,$attributes,$action,$class){
$t='<form class="form-skypostal-apibox '.$class.'" action="'.$action.'" method="post" accept-charset="UTF-8" '.$attributes.'>
	'.$formContent.'
	</form>';
	return $t;
}

function spapibox_themes_themeFormGroup($groupContent){
$t='<div class="form-row">
		'.$groupContent.'
	</div>';
	return $t;
}

function spapibox_themes_themeFormField($fieldLayoutColumn,$inputContent, $groupClass){
$t='<div class="form-group col-md-'.$fieldLayoutColumn.' '.$groupClass.'">
      '.$inputContent.'
    </div>';
    return $t;
}

function spapibox_themes_themeFormField_markup($fieldLayoutColumn,$inputContent, $groupClass){
$t='<div class="'.$groupClass.'">
      '.$inputContent.'
    </div>';
    return $t;
}


function spapibox_themes_themeFieldset($fieldsetContent,$fieldsetId,$title,$wrapperClass){
$t='<div id="'.$fieldsetId.'" class="'.$wrapperClass.'">    
        <fieldset class="panel panel-default form-wrapper" >
            <legend class="panel-heading">
                <div class="panel-title fieldset-legend">
                    <span class="title">'.$title.'</span>
                </div>
            </legend>
            <div class="panel-body">
            	'.$fieldsetContent.'
            </div>
         </fieldset>
   </div>';
   return $t;
}

function spapibox_themes_theme_box_payment_method($last_payment_method){
  $t='';
  if(!empty($last_payment_method)){
    $t='<div class="row box-payment-method">
          <div class="col-12"><span class="payment-method-title">'.__('Your current payment method','skypostal_apibox').':</span>&nbsp; <span class="payment-method-summary">'.$last_payment_method.'</span></div>          
        </div>';
  }
  return $t;
}

function spapibox_themes_theme_box_status_alert($customer_info,$inactive_only=false){
  $t='';
  if(!$inactive_only) $t=spapibox_get_message('info',__('Your account is activated','skypostal_apibox'));
  if(!$customer_info->is_active){
    $link_url=''.get_option( 'fapibox_activate_box_path' );
    $action='<a class="btn btn-primary" href='.$link_url.'>'.__('Activate your account','skypostal_apibox').'</a>';
    $title=__('Your account is not active','skypostal_apibox');
    $t='<div class="row box-status-alert">
          <div class="col text-center">'.$title.'</div>
          <div class="col text-center">'.$action.'</div>
        </div>';
    $t=spapibox_get_message('warning',$t);
  } 
  return $t;
}

function spapibox_themes_theme_login_info_box($customer_info,$box_id){

$msg_status=spapibox_themes_theme_box_status_alert($customer_info);

$t='<div class="box-info-container"><div class="row">
  <div class="col">    
      <h3>'.__('Welcome','skypostal_apibox').' '.$customer_info->customer_first_name.'</h3>
      '.$msg_status.'    
  </div>
  <div class="col"><h3>'.__('Your tax free U.S. address is','skypostal_apibox').': </h3>
  <p>
  7701 NW 15th Street<br />
  <b>Suite # '.$customer_info->customer_address[0]->ctry_iso_code.$box_id.'</b><br />
  Miami, Florida 33106<br />
  +1 (305) 436-6811<br />
  </p>
  </div>
  </div>
  </div>';
return $t;
}

function spapibox_themes_theme_consolidation_status($customer_info,$box_id){

//$msg_status=spapibox_themes_theme_box_status_alert($customer_info);



$t='<div class="box-info-container"><div class="row">
  <div class="col">    
      <h3>'.__('Welcome','skypostal_apibox').' '.$customer_info->customer_first_name.'</h3>
      '.$msg_status.'    
  </div>
  <div class="col"><h3>'.__('Your tax free U.S. address is','skypostal_apibox').': </h3>
  <p>
  7701 NW 15th Street<br />
  <b>Suite # '.$customer_info->customer_address[0]->ctry_iso_code.$box_id.'</b><br />
  Miami, Florida 33106<br />
  +1 (305) 436-6811<br />
  </p>
  </div>
  </div>
  </div>';
return $t;
}

function spapibox_themes_theme_invoice_detail_html($detail_info_post){

  $detail= '<div class="table-responsive">
  <table class="table table-striped  box-invoice-custom-detail">
    <thead><tr>
      <th class="" style="min-width:200px;">
        <span><strong>'.__('Item Description','skypostal_apibox').'</strong></span>
      </th>
      <th class="">
        <span><strong>'.__('Qty.','skypostal_apibox').'</strong></span>
      </th>
      <th class="">
        <span><strong>'.__('Item Price $','skypostal_apibox').'</strong></span>
      </th>
      <th class="">
        <span><strong>'.__('Total $','skypostal_apibox').'</strong></span>
      </th>
      <th class="">
      <span><strong>'.__('Delete','skypostal_apibox').'</strong></span>
      </th>
    </tr></thead>
  
  ';
  $idx=0;
  $label_for_0=array();
  $label_for_0[0]=array('idui'=>0, 'qty'=>'2', 'price'=>'10','desc'=>__('T-Shirt','skypostal_apibox'));  
  if(count($detail_info_post)<=0) {
    $pre_data = array();
    $pre_data[0]=array('idui'=>0, 'qty'=>'', 'price'=>'','desc'=>'');      
  }else
    $pre_data = $detail_info_post;

  $detail.='<tbody id="skpt_tbody_details">';
  
  $count=0;
  foreach($pre_data as $k=>$v){

    $idx=$count;
    $count+=1;

    $total_display=0;
    if(is_numeric($v['qty']) && is_numeric($v['price'])) $total_display=$v['qty']*$v['price'];

    $detail.='<tr id="skptinvdet-main_'.$idx.'" class="detail_display_row">        
      <td class=""  style="min-width:200px;">      
        <input type="hidden" name = "skptinvdetidx_'.$idx.'" value = "'.$idx.'" />  
        <input class="form-control  required detdesc" type="text" id="skptinvdet-desc_'.$idx.'" name="skptinvdetdesc_'.$idx.'" value="'.$v['desc'].'" placeholder="'.($idx==0 && isset($label_for_0[0])? $label_for_0[0]['desc']:'').'" >
      </td>
      <td class="">
        <input class="form-control  required detqty" type="text" onchange="skpt_qty_price('.$idx.')" id="skptinvdet-qty_'.$idx.'" name="skptinvdet-qty_'.$idx.'" value="'.$v['qty'].'" placeholder="'.($idx==0 && isset($label_for_0[0])? $label_for_0[0]['qty']:'').'">
      </td>
      <td class="">
        <input class="form-control  required detprice" type="text" onchange="skpt_chg_price('.$idx.')" id="skptinvdet-price_'.$idx.'" name="skptinvdet-price_'.$idx.'" value="'.$v['price'].'" placeholder="'.($idx==0 && isset($label_for_0[0])? $label_for_0[0]['price']:'').'">
      </td>
      <td class="">
        <span id="skptinvdet-summary_'.$idx.'">$ '.$total_display.' </span>
      </td>
      <td class="">
        <button id="skptinvdet-summary_'.$idx.'" onclick="skpt_det_remove('.$idx.')" type="button" class="btn btn-outline-danger btn-remove-inv-line">x</button>
      </td>
    </tr>
  ';
  }



  $detail.='</tbody></table></div>';

  return $detail;
}



?>