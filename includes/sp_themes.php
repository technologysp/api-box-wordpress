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

function spapibox_themes_theme_login_info_box($customer_info,$box_id){
$t='<div class="box-info-container"><div class="row">
  <div class="col"><h3>'.__('Welcome','skypostal_apibox').' '.$customer_info->customer_first_name.'</h3></div>
  <div class="col"><h3>'.__('Your tax free U.S. address is').': </h3>
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