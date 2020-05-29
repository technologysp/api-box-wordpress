<?php 
/**
 * Skypostal Init handling
 *
 * @package Skypostal_apibox
 * @subpackage Init
 * @since 1.0.0
 */

/**
 * Handles POST actions from pre-defined forms indexed by submit field ID. POST index must be defined in spapibox_init_post_actions()
 * 
 */
function spapibox_init_login_action(){
	$opts=array();
	$tools = new skypostalServices($opts);			

	$form = spapibox_form_build_login($tools, true);//Get form definition
	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);


	if( isset( $data[$results_key] ) && isset( $data[$results_key]['danger'] ) && count( $data[$results_key]['danger'] ) > 0){
		//DO NOTHING, THERE ARE PREVIOUS ERRORS
	}else{
		$pass    = sanitize_text_field( $_POST["login_password"] );
		$email   = sanitize_email( $_POST["login_email"] );				
		$result = $tools->sp_do_login($email, $pass);//Executes login service and saves session cookies (if succeeded)						
		
		if(isset($result[0]) && $result[0]->_verify && $result[0]->is_enable){
			$tools->save_login_service_session($result[0]->customer_key,$result[0]->customer_box_id,$result[0]->customer_first_name);			
			wp_redirect( $tools->_login_success_redirect_url ); exit;
		}else{
			 $_POST[$results_key]['danger'][] =array('field'=>'login_email', 'message'=>esc_html__('Invalid credentials','skypostal_apibox'));			 
		}
	}
}

function spapibox_init_customer_reg_virtual_action(){
	$_POST = spapibox_check_post($_POST);	
	$tools = new skypostalServices();		
	$form = spapibox_form_build_customer_registration_virtual($tools, true);//Get form definition

	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);	

	//EMAIL VALIDATIONS:
	if( count($_POST[$results_key])<=0 ){
		$info=$tools->sp_partner_customer_get_info(array("customer_email"=>$_POST['email']));
		if($info[0]->_verify){
			$_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Email already exists','skypostal_apibox'));
		}	
	}	
	
	//additional validations:
    if (!$tools->validateDate($_POST['date_of_birth'])) $_POST[$results_key]['danger'][] =array('field'=>'date_of_birth', 'message'=>esc_html__('Invalid Birth Date','skypostal_apibox'));
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Invalid email','skypostal_apibox'));
    if ($_POST['password'] != $_POST['confirm_password'])  $_POST[$results_key]['danger'][] =array('field'=>'password', 'message'=>esc_html__('Password does not match','skypostal_apibox'));

    if(!(is_array($_POST[$results_key]) && count($_POST[$results_key])>0)){    	
        $result=$tools->sp_customer_registration_virtual($_POST);        
        if($result[0]->_verify){
        	$tools->save_login_service_session($result[0]->customer_key,$result[0]->customer_box_id,$_POST['first_name']);
        	//Fire event
        	spapibox_events_after_virtual_registration_success($_POST);			
        	if($tools->_reg_email_mode=='default'){
        		$info=$tools->sp_partner_customer_get_info(array("customer_email"=>$_POST['email']));
				if($info[0]->_verify){
					//$_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Email already exists','skypostal_apibox'));

					$suite=$info[0]->customer_address[0]->ctry_iso_code . $result[0]->customer_box_id;
					$emaillink = $tools->get_default_email(array('suite'=>$suite, 'name'=>$_POST['first_name'],'email'=>$_POST['email']));
					if(!empty($emaillink)){
						$tools->send_html_email($emaillink,$_POST['email'],'Your account has been activated');
					}
				}	
        	}

        	wp_redirect( $tools->_login_success_redirect_url ); exit;
        }else{
        	if(isset($result[0]->error) && isset($result[0]->error->error_description) && $result[0]->error->error_description=="The email account sent is already registered and associated with a box."){
        		$_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Email already exists','skypostal_apibox'));
        	}else        	
        		$_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Registration failed. Check your data and try again.','skypostal_apibox'));
        }                
    }
}

function spapibox_init_customer_reg_default_action(){
	$_POST = spapibox_check_post($_POST);	
	$tools = new skypostalServices();		
	$form = spapibox_form_build_customer_registration_default($tools, true);//Get form definition

	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

	//additional validations:
    if (!$tools->validateDate($_POST['date_of_birth'])) $_POST[$results_key]['danger'][] =array('field'=>'date_of_birth',  'message'=>esc_html__('Invalid Birth Date','skypostal_apibox'));
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Invalid email','skypostal_apibox'));
    if ($_POST['password'] != $_POST['confirm_password'])  $_POST[$results_key]['danger'][] =array('field'=>'password',  'message'=>esc_html__('Password does not match','skypostal_apibox'));

    if(!(is_array($_POST[$results_key]) && count($_POST[$results_key])>0)){
        $result=$tools->sp_customer_registration_default($_POST);
        if($result[0]->_verify){
        	$tools->save_login_service_session($result[0]->customer_key,$result[0]->customer_box_id,$_POST['first_name']);
        	wp_redirect( $tools->_login_success_redirect_url ); exit;
        }else{
        	if(isset($result[0]->error) && isset($result[0]->error->error_description) && $result[0]->error->error_description=="The email account sent is already registered and associated with a box."){
        		$_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Email already exists','skypostal_apibox'));
        	}else        	
        		$_POST[$results_key]['danger'][] =array('field'=>'email', 'message'=>esc_html__('Registration failed. Check your data and try again.','skypostal_apibox'));
        }        
        
    }	
}

function spapibox_init_customer_update_personal_info(){	
	$_POST = spapibox_check_post($_POST);
	//echo '<pre>'.print_r($_POST, true).'</pre>';
	$tools = new skypostalServices();		
	$form = spapibox_form_build_customer_update_personal_info($tools, true);//Get form definition

	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

	//additional validations:
    if (!$tools->validateDate($_POST['date_of_birth'])) $_POST[$results_key]['danger'][] =array('field'=>'date_of_birth', 'message'=>esc_html__('Invalid Birth Date','skypostal_apibox'));

    if(!(is_array($_POST[$results_key]) && count($_POST[$results_key])>0)){
        $result=$tools->sp_customer_update_personal_info($_POST);
        if($result[0]->_verify){
        	$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('Information updated','skypostal_apibox'));
        }else
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Information not updated','skypostal_apibox'));
        
    }	
}

function spapibox_init_customer_activate_box(){	
	$_POST = spapibox_check_post($_POST);
	//echo '<pre>'.print_r($_POST, true).'</pre>';
	$tools = new skypostalServices();		
	$form = spapibox_form_build_customer_activate_box($tools, true);//Get form definition

	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

	//additional validations:    
    if(!(is_array($_POST[$results_key]) && count($_POST[$results_key])>0)){
        $result=$tools->sp_customer_activate_box($_POST);
        if($result[0]->_verify){
        	$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('Information updated','skypostal_apibox'));
        }else{
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Information not updated','skypostal_apibox'));
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>print_r($result, true));
      	}
   }
        
}	
function spapibox_init_customer_update_address_info(){	
	$_POST = spapibox_check_post($_POST);
	//echo '<pre>'.print_r($_POST, true).'</pre>';
	$tools = new skypostalServices();		
	$form = spapibox_form_build_customer_address_info($tools, true);//Get form definition

	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

	//additional validations:    

    if(!(is_array($_POST[$results_key]) && count($_POST[$results_key])>0)){
        $result=$tools->sp_customer_update_address_info($_POST);
        if($result[0]->_verify){
        	$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('Information updated','skypostal_apibox'));
        }else{
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Information not updated','skypostal_apibox'));
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>print_r($result, true));
      	}
   }
        
}	
function spapibox_init_endpoint(){
	$tools = new skypostalServices();
	switch($_POST['endpoint']){
		case 'sp_geographic_get_states':
			if(isset($_POST['country_code']) && is_numeric($_POST['country_code'])){
				$result = $tools->sp_geographic_get_states(sanitize_text_field($_POST['country_code']));					
				echo json_encode($result);
				exit();
			}				
			break;
		case 'sp_geographic_get_cities':
			if(isset($_POST['state_code']) && is_numeric($_POST['state_code'])){
				$result = $tools->sp_geographic_get_cities(sanitize_text_field($_POST['state_code']));					
				echo json_encode($result);
				exit();
			}				
			break;
	}
}

function spapibox_init_customer_update_email(){	
	$_POST = spapibox_check_post($_POST);	
	$tools = new skypostalServices();		
	if(!$tools->is_logged_in_simple()) return null;
	$form = spapibox_form_build_customer_update_email($tools, true);//Get form definition

	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

	//additional validations:
    if (!filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL)) $_POST[$results_key]['danger'][] =array('field'=>'customer_email', 'message'=>esc_html__('Invalid email','skypostal_apibox'));

    if(!(is_array($_POST[$results_key]) && count($_POST[$results_key])>0)){
        $result=$tools->sp_customer_update_email($_POST);
        if($result[0]->_verify){
        	$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('Information updated','skypostal_apibox'));
        }else
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Information not updated','skypostal_apibox'));
        
    }	
}

function spapibox_init_customer_invoice_uploader(){	
	$_POST = spapibox_check_post($_POST);
	//echo '<pre>'.print_r($_POST, true).'</pre>';
	$tools = new skypostalServices();		
	if(!$tools->is_logged_in_simple()) return null;

	$form = spapibox_form_build_customer_shipment_invoice($tools, true);//Get form definition
	//print_r($_POST);
	//Form required field validation results:
	$results_key = $form['#id'].'_result';
	$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);
	//$_POST[$results_key]=array();
	
	//trck_nmr_fol_invoice
	$target_dir = "";
	$target_file = $target_dir . basename($_FILES["trck_nmr_fol_invoice"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image	
	// Check file size
	if ($_FILES["trck_nmr_fol_invoice"]["size"] > (2*1024*1024)) {
		$_POST[$results_key]['danger'][] =array('field'=>'customer_email', 'message'=>esc_html__('File is too large. Max file size is','skypostal_apibox').' 2MB');
	    //echo "Sorry, your file is too large.";
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx") {
		$_POST[$results_key]['danger'][] =array('field'=>'customer_email', 'message'=>__('File type not allowed. Must be ','skypostal_apibox').' JPG, JPEG, PNG, GIF, PDF, DOC '.__('only','skypostal_apibox'));
	    //echo "Only JPG, JPEG, PNG, GIF, PDF, DOC files are allowed.";
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0 || (is_array($_POST[$results_key]) && count($_POST[$results_key])>0)) {
		$_POST[$results_key]['danger'][] =array('field'=>'customer_email', 'message'=>__('File not uploaded','skypostal_apibox'));
	// if everything is ok, try to upload file
	} else {
		
		$data=array("trck_nmr_fol"=>$_POST['trck_nmr_fol'],'invoice_file_name'=>$target_file);
		$result=$tools->sp_customer_upload_invoice_data($data);
		if($result[0]->_verify){
			$invoice_data = $result;
			$invoice_file_guid=$result[0]->invoice_file_guid;
			$invoice_file_name=$result[0]->invoice_file_name;

			//print_r('Rescatando al soldado: '.$invoice_file_guid);
			
			$filename = $_FILES["trck_nmr_fol_invoice"]["tmp_name"];
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			$data=array('invoice_file_guid'=>$invoice_file_guid);
			$result=$tools->sp_customer_upload_invoice_file($contents,$data);

			//echo 'Rescatando al otro soldado: '.print_r($result,true);
			
			if($result[0]->_verify){
				$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('File uploaded','skypostal_apibox').' '.$target_file);
				sapibox_events_after_invoice_uploaded_success(array('invoice_data'=>$invoice_data,'file_upload'=>$result));
			}else
				$_POST[$results_key]['danger'][] =array('field'=>'customer_email', 'message'=>__('File not uploaded','skypostal_apibox'));
		}else
			$_POST[$results_key]['danger'][] =array('field'=>'customer_email', 'message'=>__('File not uploaded','skypostal_apibox'));    
	}
	//echo ' === Y POS AL FINAL === '.print_r($_POST[$results_key],true);	
}

function spapibox_init_post_actions(){
	
	/* LOGIN FORM POSTED */ 
	if ( isset( $_POST['sp_customer_login'] ) ) {		
		spapibox_init_login_action();
		return;
	}
	/* VIRTUAL CUSTOMER REGISTRATION FORM POSTED */ 
	if ( isset( $_POST['sp_customer_reg_virtual_action'] ) ) {
		spapibox_init_customer_reg_virtual_action();
		return;
	}
	/* ENDPOINT REQUESTED */
	if (isset($_POST['endpoint']) && !empty($_POST['endpoint'])){
		spapibox_init_endpoint();
		return;
	}
	/* VIRTUAL CUSTOMER REGISTRATION FORM POSTED */ 
	if ( isset( $_POST['sp_customer_update_personal_info'] ) ) {
		spapibox_init_customer_update_personal_info();
		return;
	}
	/* DEFAULT CUSTOMER REGISTRATION FORM POSTED*/
	if ( isset( $_POST['sp_customer_reg_default_action'] ) ) {
		spapibox_init_customer_reg_default_action();
		return;
	}

	if ( isset( $_POST['sp_customer_activate_box'] ) ) {
		spapibox_init_customer_activate_box();
		return;
	}	

	if ( isset( $_POST['sp_customer_address_info'] ) ) {
		spapibox_init_customer_update_address_info();
		return;
	}	

	if ( isset( $_POST['sp_customer_update_email'] ) ) {
		spapibox_init_customer_update_email();
		return;
	}	
	if ( isset( $_POST['sp_customer_invoice_uploader'] ) ) {
		spapibox_init_customer_invoice_uploader();
		return;
	}	
	/*CHECK FOR LOGOUT*/ 
	$service_url = get_option( 'fapibox_login_logout_url' );//new skypostalServices();

	if(!empty($service_url))
	if ($_SERVER['REQUEST_URI']=='/'.$service_url.'/'){//Matches
		$service=new skypostalServices();		
		$service->save_logout_service_session();
		wp_redirect( $service->_login_no_sess_url ); exit;
	}	
}

?>