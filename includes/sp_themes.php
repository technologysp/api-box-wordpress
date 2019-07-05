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
$t='<form class="'.$class.'" action="'.$action.'" method="post" accept-charset="UTF-8" '.$attributes.'>
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

function spapibox_themes_theme_box_status_alert($customer_info,$inactive_only=false){
  $t='';
  if(!$inactive_only) $t=spapibox_get_message('info',__('Your account is activated','skypostal_apibox'));
  if(!$customer_info->is_active){
    $link_url='/'.get_option( 'fapibox_activate_box_path' );
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

?>