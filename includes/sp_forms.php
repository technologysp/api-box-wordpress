<?php
/**
 * Skypostal Forms handling
 *
 * @package Skypostal_apibox
 * @subpackage Forms
 * @since 1.0.0
 */

/**
 * Creates form array for different options and builds form HTML based in pre-defined array structure
 *
 * Main functions: 
 * - spapibox_form_render_table
 * - spapibox_form_render_group_field
 * - spapibox_form_render_group
 * - spapibox_form_validate_required_groups
 */

function spapibox_form_render_field($field_key,$field,$parent_fieldset,$formID, $filldata){
	$identifier = $field_key;
	$value = '';
	$attributes='';
	if(isset($filldata[$identifier]) && $field['type']!='password'){
		switch($field['type']){
			case 'email': $value = sanitize_email($filldata[$identifier]); break;
			default: $value = sanitize_text_field($filldata[$identifier]); break;
		}		
	}else{
		if(isset($field['default'])){
			$value=$field['default'];
		}
	} 
	if(isset($field['attributes']))
		foreach($field['attributes'] as $atrk=>$atrv)
			$attributes.=$atrk.'="'.$atrv.'"';

	$inputclass= '';

	if($field['required']){
		$inputclass.=' required';
		$inputrequiredlabel = '<span class="form-required" title="'.esc_html__('This field is required','skypostal_apibox').'">*</span>';
	} 

	if($field['type']=='hidden' ){
		$render_field= '<input class="form-control '.$inputclass.' form-text" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" value="'. $value .'" '.$attributes.' />';
        				
	}

	if($field['type']=='text' || $field['type']=='email' || $field['type']=='password' ){
		$render_field= '<div class="form-item form-group field_'.$identifier.'">
						<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
        				<input class="form-control '.$inputclass.' form-text" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" value="'. $value .'" '.$attributes.' />
        				</div>';
	}
	if($field['type']=='submit'){
		$render_field= '<div class="submit-wrapper centered field_'.$identifier.'">
        				<input class="btn btn-primary form-submit" type="'.$field['type'].'" id="'.$formID.'" name="'.$formID.'" value="'. $field['title'].'" '.$attributes.' />
       					 </div>';
	}
	if($field['type']=='radio' ){
		$render_field= '<div class="form-item form-group form-radios field_'.$identifier.'"">
						<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>';
		$child=0;
		if(isset($field['options']))
        foreach($field['options'] as $opkey=>$opval){
        	$render_field.='<div class="form-type-radio form-item-account radio item-'.$child.'">
                            <input type="radio" id="'.$identifier.$child.'" name="'.$identifier.'" value="'.$opkey.'"  class="radio" '.($opkey==$value ? 'checked="checked"': '' ).' />
                            <label for="'.$identifier.$child.'">'.$opval.$inputrequiredlabel .'</label>
                            </div>';
            $child+=1;
        }
        $render_field.='</div>';
	}
	if($field['type']=='select' ){
		$render_field= '<div class="form-item form-group form-type-select field_'.$identifier.'">
						<label for="'.$identifier.'" class="radio-inline">'.$field['title'].$inputrequiredlabel .'</label>
						<select class="form-control form-select required" id="'.$identifier.'" name="'.$identifier.'">';
		$child=0;
		if(isset($field['options']))
        foreach($field['options'] as $opkey=>$opval){
        	$render_field.='<option value="'.$opkey.'" '.($opkey==$value ? 'selected="selected"': '' ).'>'.$opval.'</option>';
            $child+=1;
        }
        $render_field.='</select></div>';
	}
	if($field['type']=='textarea'){
		$render_field= '<div class="form-item form-group field_'.$identifier.'">
						<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
        				<textarea class="form-control '.$inputclass.' form-textarea" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" '.$attributes.'>'. $value .'</textarea>
        				</div>';
	}

	if($field['type']=='checkbox'){
		$checked = 'checked="checked"';
		if(!empty($value) && is_numeric($value)){
			if($value!=1){$checked ='';}else $checked ='checked="checked"';
		}else
			if($value=='on') { $checked ='checked="checked"';}else $checked ='';		 
		
		$render_field= '<div class="form-item form-group form-type-checkbox form-checkbox field_'.$identifier.'">						
        				<input class="form-checkbox '.$inputclass.'" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" '.$attributes.' '. $checked .' />
        				<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
        				</div>';
	}

	if($field['type']=='markup' && isset($field['markup'])){$render_field = $field['markup'];}
	
	return $render_field;
}

function spapibox_form_render_table($data, $formid){
	
	$table='<div class="table-skypostal-apibox table-responsive">';

	if(isset($data['title'])) $table.='<h4>'.$data['title'].'</h4>';

	$table.='<table class="table table-striped results_'.$formid.'">';	
	$header='<thead><tr>';
	foreach($data['header'] as $head=>$val){
		$header.='<th>'.$val.'</th>';
	}
	$header .='</tr></thead>';

	$body='<tbody>';
	foreach($data['body'] as $row){
		$body.='<tr>';
			foreach($data['header'] as $head=>$val){

				$disp=$row[$head]['value'];
				if(!empty($row[$head]['link'])) $disp='<a href="'.$row[$head]['link'].'">'.$disp.'</a>';
				$body.='<td>'.$disp.'</td>';
			}
		$body.='</tr>';
	}
	$body.='</tbody>';

	$table.=$header.$body;
	$table.='</table></div>';
	return $table;
}

function spapibox_form_render($form, $filldata){

	if (empty($form['#id'])) return esc_html__('The form ID is required','skypostal_apibox');

	$file = spapibox_route_template('form.template.html');
	$template_form = file_get_contents($file);		
	$file = spapibox_route_template('form_fieldset_group.template.html');
	$template_form_fieldset = file_get_contents($file);			
	$template_form_fieldset_field = '';//file_get_contents($file);		

	$fields='';
	$fieldset_result='';
	foreach($form as $fieldset=>$fieldsetprops){
		if($fieldset=='#id' || $fieldset=='#attributes') continue;
		$current_fieldset = '';
		$fields='';
		foreach($fieldsetprops['fields'] as $field_key=>$field){
			$fields.=spapibox_form_render_field($field_key,$field, $fieldset,$form['#id'], $filldata);
		}
		//$current_fieldset = str_replace('@#content',$fields, $template_form_fieldset);
		$current_fieldset=$template_form_fieldset;
		if(isset($fieldsetprops['prefix'])) $current_fieldset =$fieldsetprops['prefix'].$current_fieldset;
		$current_fieldset = str_replace('@#title',$fieldsetprops['title'], $current_fieldset );
		$current_fieldset = str_replace('@#content',$fields, $current_fieldset);		
		if(isset($fieldsetprops['suffix'])) $current_fieldset .=$fieldsetprops['suffix'];
		$fieldset_result.= $current_fieldset;

	}

	$prevmessages='';

	if(isset($filldata[$form['#id'].'_result']) && count($filldata[$form['#id'].'_result'])>0 ){

		foreach($filldata[$form['#id'].'_result'] as $msg_type=>$msgs){
			$body='<ul>';
			foreach ($msgs as $message){
				if(isset($message['message'])) $body.='<li>'.$message['message'].'</li>';
			}
			$body.='</ul>';
			$prevmessages.=spapibox_get_message($msg_type,$body);
		}
	}
	$template_form=$prevmessages.$template_form;
	$template_form=str_replace('@sp_class',$form['#id'], $template_form);
	return str_replace('@#content',$fieldset_result, $template_form);
}

function spapibox_form_render_group_field($field_key,$field,$parent_fieldset,$formID, $filldata){
	$identifier = $field_key;
	$value = '';
	$attributes='';
	if(isset($filldata[$identifier]) && $field['type']!='password'){
		switch($field['type']){
			case 'email': $value = sanitize_email($filldata[$identifier]); break;
			default: $value = sanitize_text_field($filldata[$identifier]); break;
		}		
	}else{
		if(isset($field['default'])){
			$value=$field['default'];
		}
	} 
	if(isset($field['attributes']))
		foreach($field['attributes'] as $atrk=>$atrv)
			$attributes.=$atrk.'="'.$atrv.'"';

	$inputclass= '';
	$fieldclass='field_'.$identifier;
	if(isset($field['wrapper-class'])) $fieldclass.=' '.$field['wrapper-class'];

	/* LOOK FOR ERRORS */
	if(isset($filldata[$formID.'_result']) && count($filldata[$formID.'_result'])>0 ){

		//field
		if(isset($filldata[$formID.'_result']['danger']))
		foreach($filldata[$formID.'_result']['danger'] as $message){
			
			
			if(isset($message['field']) && $message['field']==$field_key) $inputclass.=' is-invalid'; 
			
			$prevmessages.=spapibox_get_message($msg_type,$body);
		}
	}


	if($field['required']){
		$inputclass.=' required';
		$inputrequiredlabel = '<span class="form-required" title="'.esc_html__('This field is required','skypostal_apibox').'">*</span>';
	} 

	if($field['type']=='hidden' ){
		$render_field= '<input class="form-control '.$inputclass.' form-text" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" value="'. $value .'" '.$attributes.' />';
        				
	}

	if($field['type']=='text' || $field['type']=='email' || $field['type']=='password' ){
		$render_field= 	'<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
        				<input class="form-control '.$inputclass.'" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" value="'. $value .'" '.$attributes.' />';
	}
	if($field['type']=='submit'){
		$fieldclass.=' submit-wrapper centered';
		$inlineclass='';
		$render_field='';
		if(isset($field['display']) && $field['display']=='inline') {
			$inlineclass.=' form-control';
			$render_field.=		'<label for="'.$formID.'">&nbsp;</label>';
		}

		$render_field.= '<input class="btn btn-primary form-submit '.$inlineclass.'" type="'.$field['type'].'" id="'.$formID.'" name="'.$formID.'" value="'. $field['title'].'" '.$attributes.' />';
	}

	if($field['type']=='button'){
		$fieldclass.=' submit-wrapper centered';
		$inlineclass='';
		$render_field='';		
		if(isset($field['display']) && $field['display']=='inline') {
			$inlineclass.=' form-control';
			$render_field.=		'<label for="'.$formID.'">&nbsp;</label>';
		}		
		$render_field.= '<input class="btn btn-primary '.$inlineclass.'" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" value="'. $field['title'].'" '.$attributes.' /> ';
	}


	if($field['type']=='radio' ){
		$render_field= '<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
						<div class="form-item form-group form-radios field_'.$identifier.'"">
						';
		$child=0;
		if(isset($field['options']))
        foreach($field['options'] as $opkey=>$opval){
        	$render_field.='<div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="'.$identifier.$child.'" name="'.$identifier.'" value="'.$opkey.'"  class="radio margined-radio input" '.($opkey==$value ? 'checked="checked"': '' ).' />
                            <label for="'.$identifier.$child.'">'.$opval.$inputrequiredlabel .'</label>
                            </div>';
            $child+=1;
        }
        $render_field.='</div>';
	}

	if($field['type']=='file' ){
		$render_field= '<div class="custom-file">
						<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
						<input class="form-control-file border'.$inlineclass.'" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" value="'. $field['title'].'" '.$attributes.' /> </div>';   
	}

	if($field['type']=='select' ){
		//<div class="form-item form-group form-type-select field_'.$identifier.'">
			$render_field= '<label for="'.$identifier.'" class="radio-inline">'.$field['title'].$inputrequiredlabel .'</label>
						<select class="form-control form-select required" id="'.$identifier.'" name="'.$identifier.'">';
		$child=0;
		if(isset($field['options']))
        foreach($field['options'] as $opkey=>$opval){
        	$render_field.='<option value="'.$opkey.'" '.($opkey==$value ? 'selected="selected"': '' ).'>'.$opval.'</option>';
            $child+=1;
        }
        $render_field.='</select>';//</div>;
	}
	if($field['type']=='textarea'){
		//<div class="form-item form-group field_'.$identifier.'">
			$render_field= '<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>
        				<textarea class="form-control '.$inputclass.' form-textarea" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" '.$attributes.'>'. $value .'</textarea>';//</div>;
	}

	if($field['type']=='checkbox'){
		$checked = 'checked="checked"';
		if(!empty($value) && is_numeric($value)){
			if($value!=1){$checked ='';}else $checked ='checked="checked"';
		}else
			if($value=='on') { $checked ='checked="checked"';}else $checked ='';		 
		
        	$render_field= '<input class="form-checkbox '.$inputclass.'" type="'.$field['type'].'" id="'.$identifier.'" name="'.$identifier.'" '.$attributes.' '. $checked .' />
        				<label for="'.$identifier.'">'.$field['title'].$inputrequiredlabel .'</label>';//</div>;
	}

	if($field['type']=='markup' && isset($field['markup'])){

		$render_field = $field['markup'];
	}	
	$layout_cols=12;
	$prefix=(isset($field['#prefix'])?$field['#prefix']:'');
	$suffix=(isset($field['#suffix'])?$field['#suffix']:'');
	if(!empty($field['layout-cols'])) $layout_cols=$field['layout-cols'];
	return  spapibox_themes_themeFormField($layout_cols,$prefix.$render_field.$suffix,$fieldclass);
}

function spapibox_form_render_group($form, $filldata){

	if (empty($form['#id'])) return esc_html__('The form ID is required','skypostal_apibox');

	$fields='';
	$groups='';
	$fieldset_result='';
	foreach($form as $fieldset=>$fieldsetprops){
		if($fieldset=='#id' || $fieldset=='#attributes') continue;
		$current_fieldset = '';
		$fields='';
		$groups='';

		foreach($fieldsetprops['fields'] as $group_key=>$group){
			//BOOTSTRAP LAYOUT			
			$fields='';
			foreach($group as $field_key=>$field){
				$fields.=spapibox_form_render_group_field($field_key,$field, $fieldset,$form['#id'], $filldata);
			}

			$groups.=spapibox_themes_themeFormGroup($fields);

		}		
		$current_fieldset=spapibox_themes_themeFieldset($groups,$fieldset,$fieldsetprops['title'],$fieldset);

		if(isset($fieldsetprops['prefix'])) $current_fieldset =$fieldsetprops['prefix'].$current_fieldset;
		if(isset($fieldsetprops['suffix'])) $current_fieldset .=$fieldsetprops['suffix'];
		$fieldset_result.= $current_fieldset;		
	}		

	$prevmessages='<div class="form-result-messages">';

	if(isset($filldata[$form['#id'].'_result']) && count($filldata[$form['#id'].'_result'])>0 ){

		foreach($filldata[$form['#id'].'_result'] as $msg_type=>$msgs){
			$body='<ul>';
			foreach ($msgs as $message){
				if(isset($message['message'])) $body.='<li>'.$message['message'].'</li>';
			}
			$body.='</ul>';
			$prevmessages.=spapibox_get_message($msg_type,$body);
		}
	}
	$prevmessages.='</div>';
	$form_attributes='';
	if(isset($form['#attributes'])) $form_attributes=$form['#attributes'];
	$action=$_SERVER['REQUEST_URI'];
	$template_form=$prevmessages.spapibox_themes_themeForm($fieldset_result, $form_attributes,$action,$form['#id']);	
	return $template_form; 
}

function spapibox_form_validate_required_groups($form, $data){
	$messages=array();

	foreach($form as $form_category){		
		if(isset($form_category['fields']))			
			foreach($form_category['fields'] as $groupkey=>$groupfields)
			foreach($groupfields as $field_key=>$field_options){				
				if(isset($field_options['required']) && $field_options['required']==true && $field_options['type']!='file'){					
					//Field is required
					$error=false;
					if(!isset($data[$field_key])){
						$error=true;	
					}else{
						if(empty(trim($data[$field_key]))) $error=true;	
					} 
					if($error)
						$messages['danger'][]=array('field'=>$field_key, 'message'=>$form_category['title'].' - '.$field_options['title'].' '.esc_html__('is required','skypostal_apibox'));
				}
			}
	}	
	return $messages;
}

function spapibox_form_validate_required($form, $data){
	$messages=array();

	foreach($form as $form_category){		
		if(isset($form_category['fields']))			
			foreach($form_category[fields] as $field_key=>$field_options){				
				if(isset($field_options['required']) && $field_options['required']==true){					
					//Field is required
					$error=false;
					if(!isset($data[$field_key])){
						$error=true;	
					}else{
						if(empty(trim($data[$field_key]))) $error=true;	
					} 
					if($error)
						$messages['danger'][]=array('field'=>$field_key, 'message'=>$form_category['title'].' - '.$field_options['title'].' '.esc_html__('is required','skypostal_apibox'));
				}
			}
	}	
	return $messages;
}
function spapibox_form_build_customer_registration_default($skypostalServices_instance, $definition_only=false){
	return spapibox_form_build_customer_registration($skypostalServices_instance,'default', true, $definition_only);
}
function spapibox_form_build_customer_registration_virtual($skypostalServices_instance, $definition_only=false){
	return spapibox_form_build_customer_registration($skypostalServices_instance,'virtual', true, $definition_only);
}
function spapibox_form_build_customer_registration($skypostalServices_instance, $type, $bysteps=false, $definition_only=false){
	//Override multistep options:
	$opt = get_option( 'fapibox_reg_forms_multistep' );
	$bysteps= false;
	if(is_array($opt) && count($opt)>0){
		$bysteps= $opt[0]=='single' ? false:true;	
	}		

	$form=array();
	$form_title='';
	if($type=='virtual') {
		$form['#id']='sp_customer_reg_virtual_action';		
	}else
		$form['#id']='sp_customer_reg_default_action';

	$form['account_information']=array(
		"prefix"=>($bysteps ? '<div id="form_section1">':''),
		"suffix"=>($bysteps ? '</div>':''),
		"title"=>esc_html__("Account Information",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
				"group1"=>array( 
					"email"=>array("title"=>esc_html__("Email",'skypostal_apibox'), "type"=>"email", "required"=>true, "layout-cols"=>"12", "attributes"=>array("size"=>63, "maxlength"=>129))
				),
				"group2"=>array(
					"first_name"=>array("title"=>esc_html__("First name",'skypostal_apibox'), "type"=>"text", "required"=>true, "layout-cols"=>"6"),
					"last_name"=>array("title"=>esc_html__("Last name",'skypostal_apibox'), "type"=>"text", "required"=>true, "layout-cols"=>"6")
				),
				"group3"=>array(
					"password"=>array("title"=>esc_html__("Password",'skypostal_apibox'), "type"=>"password", "required"=>true, "layout-cols"=>"6"),
					"confirm_password"=>array("title"=>esc_html__("Confirm Password",'skypostal_apibox'), "type"=>"password", "required"=>true, "layout-cols"=>"6")
				),
				"group4"=>array(
					"date_of_birth"=>array("title"=>esc_html__("Date of Birth",'skypostal_apibox'), "type"=>"text", "required"=>true, "layout-cols"=>"6",  "attributes"=>array("autocomplete"=>"off") ),
					"gender"=>array("title"=>esc_html__("Gender",'skypostal_apibox'), "type"=>"radio", "required"=>true, "layout-cols"=>"6", "options"=>array("M"=>esc_html__("Male","skypostal_apibox"), "F"=>esc_html__("Female","skypostal_apibox")))
				)
		)
	);
	if($type=='virtual') $form['account_information']['fields']["group5"]=array("tax_number"=>array("title"=>esc_html__("TAX Identifier",'skypostal_apibox'), "type"=>"text", "required"=>false));

	if($bysteps) $form['account_information']['fields']["group6"]=array("nextbuttons1"=>array("title"=>esc_html__('Next','skypostal_apibox'), "type"=>"button", "required"=>false,"attributes"=>array("onclick"=>"sup_next(this)")));	

	$countries=array();
	if(!$definition_only) $countries=$skypostalServices_instance->sp_geographic_get_countries();
	$states=array();
	if(!$definition_only) if(isset($_POST['address_country'])) $states=$skypostalServices_instance->sp_geographic_get_states($_POST['address_country']);
	$cities=array();
	if(!$definition_only) if(isset($_POST['address_state'])) $cities=$skypostalServices_instance->sp_geographic_get_cities($_POST['address_state']);
	$form['delivery_address']=array(
		"prefix"=>($bysteps ? '<div id="form_section2">':''),
		"suffix"=>($bysteps ? '</div>':''),
		"title"=>esc_html__("Delivery Address",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 
				"address_country"=>array("title"=>esc_html__("Country",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$countries, "layout-cols"=>"4"),
				"address_state"=>array("title"=>esc_html__("State / Province",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$states, "layout-cols"=>"4"),
				"address_city"=>array("title"=>esc_html__("City",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$cities, "layout-cols"=>"4")
			),			
			"group3"=>array( 	
				"address_address"=>array("title"=>esc_html__("Address",'skypostal_apibox'), "type"=>"textarea", "required"=>true)
			),
			"group4"=>array( 	
				"address_region"=>array("title"=>esc_html__("Region",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"6"),
				"address_postal_code"=>array("title"=>esc_html__("Postal Code",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"6")	
			),
			"group5"=>array( 	
				"address_id_number"=>array("title"=>esc_html__("Identification Number",'skypostal_apibox'), "type"=>"text", "required"=>false)	
				)
		)
	);


	$buttons='<input type="button" class="btn btn-secondary" onclick="sup_prev(this)" value="'.__('Prev', 'skypostal_apibox').'" />&nbsp;<input type="button" class="btn btn-primary" onclick="sup_next(this)" value="'.__('Next','skypostal_apibox').'" />';

	if($bysteps) $form['delivery_address']['fields']["group6"]=array(
		"prevbuttons2"=>array("markup"=>$buttons, "type"=>"markup", "required"=>false,"noattributes"=>array("onclick"=>"sup_prev(this)"),"layout-cols"=>"12", "wrapper-class"=>"centered")		
	);	

	$form['account_telephone']=array(
		"prefix"=>($bysteps ? '<div id="form_section3">':''),
		
		"suffix"=>'',
		"title"=>__('Phone', 'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 	
				"account_phone_country"=>array("title"=>esc_html__("Country code",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"3","attributes"=>array("maxlength"=>3)),
				"account_phone_number"=>array("title"=>esc_html__("Number",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"6","attributes"=>array("maxlength"=>12)),
				"account_phone_ext"=>array("title"=>esc_html__("Extension",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"3","attributes"=>array("maxlength"=>3))
				)
		)
	);
	
	$form['account_cellphone']=array(
		"prefix"=>'',
		"suffix"=>'',
		"title"=>__('Cellphone', 'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 	
				"account_cellphone_country"=>array("title"=>esc_html__("Country code",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"3","attributes"=>array("maxlength"=>3)),
				"account_cellphone_number"=>array("title"=>esc_html__("Number",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"6","attributes"=>array("maxlength"=>23)),
				"account_cellphone_ext"=>array("title"=>esc_html__("Extension",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"3","attributes"=>array("maxlength"=>3))
				)
		)
	);
	


	$buttons=($bysteps ?'<input type="button" class="btn btn-secondary" onclick="sup_prev(this)" value="'.__('Prev','skypostal_apibox').'" />&nbsp;':'');
	$form['submission']=array(
		"suffix"=>($bysteps ? '</div>':''),
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 
				"submission_check_newsletter"=>array("title"=>esc_html__("Yes, I would like to receive specials offers and important information regarding my address.",'skypostal_apibox'), "type"=>"checkbox", "required"=>false),
				"submission_terms"=>array("markup"=>'<div>'.__('By submitting, you accept the','skypostal_apibox').' <a target="_blank" href="/'.$skypostalServices_instance->_terms_conditions_path.'">'.__('terms and conditions','skypostal_apibox').'</a>', "type"=>"markup", "required"=>false),				
				),
			"group2"=>array(		 	
				$form['#id']=>array("title"=>esc_html__("Register customer",'skypostal_apibox'), "type"=>"submit", "required"=>true, "wrapper-class"=>"centered","#prefix"=>$buttons)
		 		)
		)
	);

	return $form;
}

function spapibox_form_build_customer_update_personal_info($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_update_personal_info';
	$form['account_information']=array(
		"title"=>esc_html__("Personal Information",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(			
			"group1"=>array( 	
				"first_name"=>array("title"=>esc_html__("First name",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"6"),
				"last_name"=>array("title"=>esc_html__("Last name",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"6")
			),
			"group2"=>array( 
				"address_id_number"=>array("title"=>esc_html__("Identification Number",'skypostal_apibox'), "type"=>"text", "required"=>false, "layout-cols"=>"4"),	
				"date_of_birth"=>array("title"=>esc_html__("Date of Birth",'skypostal_apibox'), "type"=>"text", "required"=>true, "layout-cols"=>"4","attributes"=>array("autocomplete"=>"off")),
				"gender"=>array("title"=>esc_html__("Gender",'skypostal_apibox'), "type"=>"radio", "required"=>true, "options"=>array("M"=>__("Male",'skypostal_apibox'), "F"=>__("Female",'skypostal_apibox')), "layout-cols"=>"4")	
			)
		)
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 			
				$form['#id']=>array("title"=>esc_html__("Update",'skypostal_apibox'), "type"=>"submit", "required"=>true)
				)
		)
	);
	return $form;
}

function spapibox_form_build_customer_activate_box($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_activate_box';
	
	$cctypes=array("VISA"=>"VISA", "DINERS"=>"DINERS", "AMEX"=>"AMERICAN EXPRESS", "MASTER"=>"MASTERCARD");
	for($i=1;$i<13;$i++){
		$months[$i]=($i<10 ? '0'.$i:$i);
	}
	$year=date("Y");
	for($i=1;$i<10;$i++){
		$years[$year]=$year;
		$year+=1;
	}
	
	$form['account_information']=array(
		"title"=>esc_html__("Payment 	Information",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 			
				"cc_type_name"=>array("title"=>esc_html__("Type",'skypostal_apibox'), "type"=>"select", "required"=>true,  "options"=>$cctypes )
			),
			"group2"=>array( 
				"cc_holder_name"=>array("title"=>esc_html__("Name as it appears on card",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"6"),
				"cc_number"=>array("title"=>esc_html__("Number",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"6")
			),
			"group3"=>array( 
				"cc_expiration_month"=>array("title"=>esc_html__("Expiration month",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$months,"layout-cols"=>"4"),
				"cc_expiration_year"=>array("title"=>esc_html__("Expiration year",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$years,"layout-cols"=>"4"),
				"cc_security_code"=>array("title"=>esc_html__("Security code",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"4")
			)
		)
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 
				$form['#id']=>array("title"=>esc_html__("Update",'skypostal_apibox'), "type"=>"submit", "required"=>true)
				)
			)
	);
	return $form;
}

function spapibox_form_build_customer_address_info($skypostalServices_instance,$data, $definition_only=false){
	$form=array();	
	
	$form['#id']='sp_customer_address_info';		
		
	$countries=array();
	if(!$definition_only) $countries=$skypostalServices_instance->sp_geographic_get_countries();
	$states=array();
	if(!$definition_only) if(isset($data['address_country'])) $states=$skypostalServices_instance->sp_geographic_get_states($data['address_country']);
	$cities=array();
	if(!$definition_only) if(isset($data['address_state'])) $cities=$skypostalServices_instance->sp_geographic_get_cities($data['address_state']);
	$form['delivery_address']=array(
		"title"=>esc_html__("Delivery Address",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 
				"address_country"=>array("title"=>esc_html__("Country",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$countries, "layout-cols"=>"4"),
				"address_state"=>array("title"=>esc_html__("State / Province",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$states, "layout-cols"=>"4"),
				"address_city"=>array("title"=>esc_html__("City",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$cities, "layout-cols"=>"4")
			),			
			"group3"=>array( 	
				"address_address"=>array("title"=>esc_html__("Address",'skypostal_apibox'), "type"=>"textarea", "required"=>true)
			),
			"group4"=>array( 	
				"address_region"=>array("title"=>esc_html__("Region",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"6"),
				"address_postal_code"=>array("title"=>esc_html__("Postal Code",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"6")	
			),
			"group5"=>array( 	
				"address_id_number"=>array("title"=>esc_html__("Identification Number",'skypostal_apibox'), "type"=>"text", "required"=>false)	
				)
		)
	);
	
	$form['account_cellphone']=array(
		"prefix"=>'<div class="phone-section">',
		"suffix"=>'</div>',
		"title"=>__('Cellphone','skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 	
				"account_cellphone_country"=>array("title"=>esc_html__("Country code",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"3"),
				"account_cellphone_number"=>array("title"=>esc_html__("Number",'skypostal_apibox'), "type"=>"text", "required"=>true,"layout-cols"=>"6"),
				"account_cellphone_ext"=>array("title"=>esc_html__("Extension",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"3")
				)
		)
	);
	
	$form['account_telephone']=array(
		"prefix"=>'<div class="phone-section">',
		"suffix"=>'</div>',
		"title"=>__('Phone', 'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(
			"group1"=>array( 	
				"account_phone_country"=>array("title"=>esc_html__("Country code",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"3"),
				"account_phone_number"=>array("title"=>esc_html__("Number",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"6"),
				"account_phone_ext"=>array("title"=>esc_html__("Extension",'skypostal_apibox'), "type"=>"text", "required"=>false,"layout-cols"=>"3")
				)
		)
	);
	
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(		
			"group1"=>array( 			
				$form['#id']=>array("title"=>esc_html__("Update",'skypostal_apibox'), "type"=>"submit", "required"=>true)
				)
		)
	);
	return $form;
}

function spapibox_form_build_customer_get_shipments($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_get_shipments';

	$d_end=new DateTime();
	$d_start=new DateTime();
	$d_start->modify('-30 day');

	$form['account_information']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(				
			"group1"=>array( 
				"start_date"=>array("title"=>esc_html__("Start date",'skypostal_apibox'), "type"=>"text", "required"=>true, "default"=> $d_start->format('Y-m-d'), "layout-cols"=>"4"),
				"end_date"=>array("title"=>esc_html__("End date",'skypostal_apibox'), "type"=>"text", "required"=>true, "default"=>$d_end->format('Y-m-d'), "layout-cols"=>"4"),
				$form['#id']=>array("title"=>esc_html__("Search",'skypostal_apibox'), "type"=>"submit", "display"=>"inline", "required"=>true, "layout-cols"=>"4")
				)
			)					
	);
	return $form;
}

function spapibox_form_build_customer_get_shipment_info($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_get_shipments';

	$d_end=new DateTime();
	$d_start=new DateTime();
	$d_start->modify('-30 day');

	$awbdefault='';
	if(isset($_GET['awb']) && is_numeric($_GET['awb'])) $awbdefault=sanitize_text_field($_GET['awb']);

	$form['account_information']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(				
			"group1"=>array( 
				"trck_nmr_fol"=>array("title"=>esc_html__("Airway Bill Number",'skypostal_apibox'), "type"=>"text", "required"=>true, "default"=> $awbdefault, "layout-cols"=>"9"),
				$form['#id']=>array("title"=>esc_html__("Search",'skypostal_apibox'), "type"=>"submit", "required"=>true, "display"=>"inline", "layout-cols"=>"3")
			)
		)
	);
	return $form;
}

function spapibox_form_build_customer_shipment_invoice($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_invoice_uploader';
	$form['#attributes']=' enctype="multipart/form-data" ';
	
	$d_end=new DateTime();
	$d_start=new DateTime();
	$d_start->modify('-30 day');

	$awbdefault='';
	if(isset($_GET['awb']) && is_numeric($_GET['awb'])) $awbdefault=sanitize_text_field($_GET['awb']);

	$form['account_information']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(				
			"group1"=>array( 
				"trck_nmr_fol_invoice"=>array("title"=>esc_html__("Invoice File",'skypostal_apibox'), "type"=>"file", "required"=>true, "default"=> $awbdefault, "layout-cols"=>"12"),				
				"trck_nmr_fol"=>array("title"=>esc_html__("trck_nmr_fol",'skypostal_apibox'), "type"=>"hidden", "required"=>true)
			)
		)
	);
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				$form['#id']=>array("title"=>esc_html__("Upload",'skypostal_apibox'), "type"=>"submit", "required"=>true)
			)
		)
	);
	return $form;
}

function spapibox_form_build_customer_update_email($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_update_email';
	$form['account_information']=array(
		"title"=>"Email",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				"customer_email"=>array("title"=>esc_html__("Email",'skypostal_apibox'), "type"=>"email", "required"=>true, "attributes"=>array("size"=>63, "maxlength"=>129)),
				"customer_current_email"=>array("title"=>esc_html__("Email",'skypostal_apibox'), "type"=>"hidden", "required"=>true, "attributes"=>array("size"=>63, "maxlength"=>129))
			)
		)
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				$form['#id']=>array("title"=>esc_html__("Update",'skypostal_apibox'), "type"=>"submit", "required"=>true)
			)
		)
	);
	return $form;
}
	

function spapibox_form_build_customer_recover_password_update($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_recover_password_update';
	$form['account_information']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array(
					"password"=>array("title"=>esc_html__("Password",'skypostal_apibox'), "type"=>"password", "required"=>true, "layout-cols"=>"6"),
					"confirm_password"=>array("title"=>esc_html__("Confirm Password",'skypostal_apibox'), "type"=>"password", "required"=>true, "layout-cols"=>"6")
			)
		)
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				$form['#id']=>array("title"=>esc_html__("Update",'skypostal_apibox'), "type"=>"submit", "required"=>true)
			)
		)
	);
	return $form;
}
	

function spapibox_form_build_login($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_login';
	$form['login']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				"login_email"=>array("title"=>esc_html__("Email",'skypostal_apibox'), "type"=>"email", "required"=>true, "attributes"=>array("size"=>63, "maxlength"=>129)),
				"login_password"=>array("title"=>esc_html__("Password",'skypostal_apibox'), "type"=>"password", "required"=>true, "attributes"=>array("size"=>63, "maxlength"=>129)),
				)				
		)		
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				$form['#id']=>array("title"=>esc_html__("Login",'skypostal_apibox'), "type"=>"submit", "required"=>true)
			)
		)
	);
	return $form;
}

function spapibox_form_build_customer_recover_password_code($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_recover_password_code';
	$form['recovery']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				"customer_email"=>array("title"=>esc_html__("Email",'skypostal_apibox'), "type"=>"email", "required"=>true, "attributes"=>array("size"=>63, "maxlength"=>129)),
				"customer_current_email"=>array("title"=>esc_html__("Email",'skypostal_apibox'), "type"=>"hidden", "required"=>true, "attributes"=>array("size"=>63, "maxlength"=>129))
			)
		)
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 				
				$form['#id']=>array("title"=>esc_html__("Send Code",'skypostal_apibox'), "type"=>"submit", "required"=>true)
			)
		)
	);
	return $form;
}

function spapibox_form_build_calculator($skypostalServices_instance, $definition_only=false){
	$form=array();
	$form['#id']='sp_customer_calc_form';
	
	$countries=array();
	if(!$definition_only) $countries=$skypostalServices_instance->sp_geographic_get_countries();
	$states=array();
	if(!$definition_only) if(isset($_POST['address_country'])) $states=$skypostalServices_instance->sp_geographic_get_states($_POST['address_country']);
	$cities=array();
	if(!$definition_only) if(isset($_POST['address_state'])) $cities=$skypostalServices_instance->sp_geographic_get_cities($_POST['address_state']);

	$categories=array();
	$categories = $skypostalServices_instance->sp_shipment_get_family_products();	

	$weight_types=array('KG'=>esc_html__("Kilograms",'skypostal_apibox'),'LB'=>esc_html__("Pounds",'skypostal_apibox'));
	$dim_types=array('CM'=>esc_html__("Centimeters",'skypostal_apibox'),'IN'=>esc_html__("Inches",'skypostal_apibox'));	

	$form['destination']=array(
		"title"=>esc_html__("Destination",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 
				"address_country"=>array("title"=>esc_html__("Country",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$countries, "layout-cols"=>"4"),
				"address_state"=>array("title"=>esc_html__("State / Province",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$states, "layout-cols"=>"4"),
				"address_city"=>array("title"=>esc_html__("City",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$cities, "layout-cols"=>"4")
			)			
		)
	);
	$form['product_information']=array(
		"title"=>esc_html__("Product Information",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(	
			"group2"=>array( 
				"address_zipcode"=>array("title"=>esc_html__("Zipcode",'skypostal_apibox'), "type"=>"text", "required"=>false,  "layout-cols"=>"4"),
				"weight_type"=>array("title"=>esc_html__("Weight Type",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$weight_types, "layout-cols"=>"4"),
				"weight"=>array("title"=>esc_html__("Weight",'skypostal_apibox'), "type"=>"text", "required"=>true,  "layout-cols"=>"4")
			),			
			"group4"=>array( 
				"category"=>array("title"=>esc_html__("Category",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$categories, "layout-cols"=>"8"),
				"price_value"=>array("title"=>esc_html__("Product Value",'skypostal_apibox'), "type"=>"text", "required"=>true, "layout-cols"=>"4")				
			)
		)
	);
	$form['dimentional_weight']=array(
		"title"=>esc_html__("Dimentional Weight",'skypostal_apibox'),
		"attributes"=>array(),
		"fields"=>array(	
			"group3"=>array( 
				"dimension_type"=>array("title"=>esc_html__("Dimensions",'skypostal_apibox'), "type"=>"select", "required"=>true, "options"=>$dim_types, "layout-cols"=>"3"),
				"dim_length"=>array("title"=>esc_html__("Length",'skypostal_apibox'), "type"=>"text", "required"=>false,  "layout-cols"=>"3"),
				"dim_width"=>array("title"=>esc_html__("Width",'skypostal_apibox'), "type"=>"text", "required"=>false,  "layout-cols"=>"3"),
				"dim_height"=>array("title"=>esc_html__("Height",'skypostal_apibox'), "type"=>"text", "required"=>false,  "layout-cols"=>"3")				
			)
		)
	);
		
	$form['submission']=array(
		"title"=>"",
		"attributes"=>array(),
		"fields"=>array(	
			"group1"=>array( 
				$form['#id']=>array("title"=>esc_html__("Calculate",'skypostal_apibox'), "type"=>"submit", "required"=>true)
				)
			)
	);
	return $form;
}


?>